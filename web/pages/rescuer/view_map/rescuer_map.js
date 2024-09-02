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
            // Add base marker
            L.marker([baseData.latitude, baseData.longitude], {icon: L.icon({iconUrl: 'path_to_base_icon.png', iconSize: [25, 41]})})
                .addTo(map)
                .bindPopup('<b>Base Location</b>');
        }
    });


fetch('fetch_rescuer_map_data.php')
    .then(response => response.json())
    .then(data => {
        // Add vehicle marker
        if (data.vehicle) {
            vehicleMarker = L.marker([data.vehicle.latitude, data.vehicle.longitude], {draggable: false})
                .addTo(map)
                .bindPopup('<b>My Vehicle</b>');
        }// Add request markers
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

// Add offer markers
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

        // Add lines connecting the vehicle to assigned requests or offers
        if (document.getElementById('showLines').checked) {
            data.tasks.forEach(task => {
                var target = task.type === 'request' ? data.requests.find(r => r.id === task.id) : data.offers.find(o => o.id === task.id);
                if (target) {
                    var line = L.polyline([
                        [data.vehicle.latitude, data.vehicle.longitude],
                        [target.latitude, target.longitude]
                    ], {color: 'blue'}).addTo(map);

                    lines.push(line);
                }
            });
        }
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
        alert('Request has been assigned to your vehicle.');
        location.reload();
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
        alert('Offer has been assigned to your vehicle.');
        location.reload();
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
        alert(data); // Εμφανίζει το ακριβές μήνυμα που επιστρέφει η PHP
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
// Function to fetch and display current tasks in the sidebar
function loadCurrentTasks() {
    fetch('current_task.php')
        .then(response => response.json())
        .then(data => {
            const sidebar = document.getElementById('taskList'); // Υποθέτοντας ότι η `id` είναι 'taskList'
            sidebar.innerHTML = ''; // Clear existing tasks
            
            if (data.length === 0) {
                sidebar.innerHTML = '<p>No current tasks.</p>';
                return;
            }

            data.forEach(task => {
                let taskElement = document.createElement('div');
                taskElement.className = 'task-item'; // Ensure this matches your CSS

                let content = '';

                if (task.type === 'request') {
                    // Request task
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
                    // Offer task
                    content = `
                        <div>
                            <h3>Offer</h3>
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

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', loadCurrentTasks);
