var map = L.map('map').setView([37.9838, 23.7275], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var vehicleMarker;
var changingVehicleLocation = false;

var requestMarkers = [];
var offerMarkers = [];
var lines = [];

// Fetch base location and add marker to map
fetch('fetch_base_location.php')
    .then(response => response.json())
    .then(baseData => {
        if (baseData.latitude && baseData.longitude) {
            L.marker([baseData.latitude, baseData.longitude], {icon: L.icon({iconUrl: 'path_to_base_icon.png', iconSize: [25, 41]})})
                .addTo(map)
                .bindPopup('<b>Base Location</b>');
        }
    });

fetch('fetch_rescuer_map_data.php')
    .then(response => response.json())
    .then(data => {
        if (data.vehicle) {
            vehicleMarker = L.marker([data.vehicle.latitude, data.vehicle.longitude], {draggable: false})
                .addTo(map)
                .bindPopup('<b>My Vehicle</b>');
        }

        data.requests.forEach(request => {
            var color = request.status === 'pending' ? 'black' : 'red';
            var marker = L.marker([request.latitude, request.longitude], {
                icon: L.divIcon({className: 'request-icon', html: `<div style="background: ${color}; width: 25px; height: 41px; border-radius: 50%;"></div>`})
            })
            .bindPopup(`
                <b>Request from:</b> ${request.citizen_username || 'Unknown'}<br>
                <b>Item:</b> ${request.item || 'N/A'}<br>
                <b>Quantity:</b> ${request.quantity || 'N/A'}<br>
                <b>Request Date:</b> ${request.request_date || 'N/A'}<br>
                <button onclick="assignRequest(${request.id})">Take Request</button><br>
                <button onclick="completeTask('request', ${request.id})" ${distanceToTask(request.latitude, request.longitude) > 50 ? 'disabled' : ''}>Complete</button><br>
                <button onclick="cancelTask('request', ${request.id})">Cancel</button>
            `)
            .addTo(map);

            requestMarkers.push({ marker, request });
        });

        data.offers.forEach(offer => {
            var color = offer.status === 'pending' ? 'yellow' : 'orange';
            var marker = L.marker([offer.latitude, offer.longitude], {
                icon: L.divIcon({className: 'offer-icon', html: `<div style="background: ${color}; width: 25px; height: 41px; border-radius: 50%;"></div>`})
            })
            .bindPopup(`
                <b>Offer from:</b> ${offer.citizen_username || 'Unknown'}<br>
                <b>Item:</b> ${offer.item || 'N/A'}<br>
                <b>Quantity:</b> ${offer.quantity || 'N/A'}<br>
                <b>Offer Date:</b> ${offer.offer_date || 'N/A'}<br>
                <button onclick="assignOffer(${offer.id})">Take Offer</button><br>
                <button onclick="completeTask('offer', ${offer.id})" ${distanceToTask(offer.latitude, offer.longitude) > 50 ? 'disabled' : ''}>Complete</button><br>
                <button onclick="cancelTask('offer', ${offer.id})">Cancel</button>
            `)
            .addTo(map);

            offerMarkers.push({ marker, offer });
        });

        drawLines(data.tasks);
    });

function drawLines(tasks) {
    // Clear existing lines
    lines.forEach(line => map.removeLayer(line));
    lines = [];

    // Check if the checkbox for lines is checked
    if (document.getElementById('showLines').checked && vehicleMarker) {
        tasks.forEach(task => {
            var target = task.type === 'request' ? requestMarkers.find(r => r.request.id === task.task_id) : offerMarkers.find(o => o.offer.id === task.task_id);
            if (target) {
                var line = L.polyline([
                    [vehicleMarker.getLatLng().lat, vehicleMarker.getLatLng().lng],
                    [target.marker.getLatLng().lat, target.marker.getLatLng().lng]
                ], {color: 'blue'}).addTo(map);
                lines.push(line);
            }
        });
    }
}

// Toggle the lines when the checkbox is changed
document.getElementById('showLines').addEventListener('change', function () {
    fetch('fetch_rescuer_map_data.php')
        .then(response => response.json())
        .then(data => {
            drawLines(data.tasks);
        });
});

document.getElementById('setVehicleLocationButton').addEventListener('click', function () {
    if (vehicleMarker) {
        vehicleMarker.dragging.enable();
        changingVehicleLocation = true;
        document.getElementById('saveVehicleLocationButton').style.display = 'block';
    }
});

document.getElementById('saveVehicleLocationButton').addEventListener('click', function () {
    if (changingVehicleLocation && vehicleMarker) {
        var newLatLng = vehicleMarker.getLatLng();
        fetch('update_vehicle_location.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                latitude: newLatLng.lat,
                longitude: newLatLng.lng
            })
        })
        .then(response => response.text())
        .then(data => {
            vehicleMarker.dragging.disable();
            changingVehicleLocation = false;
            document.getElementById('saveVehicleLocationButton').style.display = 'none';
            alert('Vehicle location updated successfully.');
        });
    }
});

function assignRequest(requestId) {
    fetch('assign_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            task_id: requestId,
            task_type: 'request'
        })
    })
    .then(response => response.text())
    .then(data => {
        // Ελέγχουμε αν η απάντηση περιέχει κάποιο μήνυμα σφάλματος
        if (data.includes("Error:")) {
            alert(data); // Προβάλει το ακριβές μήνυμα σφάλματος που στέλνει το PHP script
        } else {
            alert('Request has been assigned to your vehicle.');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the request.');
    });
}

function assignOffer(offerId) {
    fetch('assign_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            task_id: offerId,
            task_type: 'offer'
        })
    })
    .then(response => response.text())
    .then(data => {
        // Ελέγχουμε αν η απάντηση περιέχει κάποιο μήνυμα σφάλματος
        if (data.includes("Error:")) {
            alert(data); // Προβάλει το ακριβές μήνυμα σφάλματος που στέλνει το PHP script
        } else {
            alert('Offer has been assigned to your vehicle.');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the offer.');
    });
}


function completeTask(type, taskId) {
    fetch('complete_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            task_type: type,
            task_id: taskId
        })
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while completing the task.');
    });
}

function cancelTask(type, taskId) {
    fetch('cancel_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            task_type: type,
            task_id: taskId
        })
    })
    .then(response => response.text())
    .then(data => {
        alert(`${type.charAt(0).toUpperCase() + type.slice(1)} task has been canceled.`);
        location.reload();
    });
}

function distanceToTask(lat, lng) {
    if (!vehicleMarker) return Infinity;
    var vehicleLatLng = vehicleMarker.getLatLng();
    var distance = map.distance(vehicleLatLng, L.latLng(lat, lng));
    return distance;
}

function loadCurrentTasks() {
    fetch('current_task.php')
        .then(response => response.json())
        .then(data => {
            const sidebar = document.getElementById('taskList'); 
            sidebar.innerHTML = ''; 
            
            if (data.length === 0) {
                sidebar.innerHTML = '<p>No current tasks.</p>';
                return;
            }

            data.forEach(task => {
                let taskElement = document.createElement('div');
                taskElement.className = 'task-item'; 

                let content = '';

                if (task.type === 'request') {
                    content = `
                        <div>
                            <h3>Request</h3>
                            <p><b>From:</b> ${task.citizen_username}</p>
                            <p><b>Date:</b> ${task.request_date}</p>
                            <p><b>Item:</b> ${task.item_id}</p>
                            <p><b>Quantity:</b> ${task.quantity}</p>
                            <p><b>Status:</b> ${task.request_status}</p>
                            <button onclick="completeTask('request', ${task.request_id})">Complete</button>
                            <button onclick="cancelTask('request', ${task.request_id})">Cancel</button>
                        </div>
                    `;
                } else if (task.type === 'offer') {
                    content = `
                        <div>
                            <h3>Offer</h3>
                            <p><b>From:</b> ${task.citizen_username}</p> <!-- Προσθέτουμε το όνομα του χρήστη -->
                            <p><b>Date:</b> ${task.request_date}</p>
                            <p><b>Item:</b> ${task.item_id}</p>
                            <p><b>Quantity:</b> ${task.quantity}</p>
                            <p><b>Status:</b> ${task.request_status}</p>
                            <button onclick="completeTask('offer', ${task.request_id})">Complete</button>
                            <button onclick="cancelTask('offer', ${task.request_id})">Cancel</button>
                        </div>
                    `;
                }

                taskElement.innerHTML = content;
                sidebar.appendChild(taskElement);
            });
        })
        .catch(error => console.error('Error fetching current tasks:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    loadCurrentTasks();

    // Event listeners for the filters
    document.getElementById('showPendingRequests').addEventListener('change', function () {
        updateMarkers();
    });

    document.getElementById('showActiveRequests').addEventListener('change', function () {
        updateMarkers();
    });

    document.getElementById('showOffers').addEventListener('change', function () {
        updateMarkers();
    });
});

// Update the visibility of markers based on the filters
function updateMarkers() {
    // Update request markers
    requestMarkers.forEach(({ marker, request }) => {
        if (document.getElementById('showPendingRequests').checked && request.status === 'pending') {
            marker.addTo(map);
        } else if (document.getElementById('showActiveRequests').checked && request.status === 'active') {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });

    // Update offer markers
    offerMarkers.forEach(({ marker, offer }) => {
        if (document.getElementById('showOffers').checked) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });

    // Redraw lines based on the updated markers
    fetch('fetch_rescuer_map_data.php')
        .then(response => response.json())
        .then(data => {
            drawLines(data.tasks);
        });
}
