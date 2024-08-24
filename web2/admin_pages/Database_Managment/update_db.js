document.getElementById('urlUploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var jsonUrl = 'http://usidas.ceid.upatras.gr/web/2023/export.php';

    fetch('proxy.php?url=' + encodeURIComponent(jsonUrl))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Directly parse the response as JSON
        })
        .then(data => {
            updateDatabase(data);
        })
        .catch(error => console.error('Error loading JSON from URL:', error));
});

document.getElementById('fileUploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var file = document.getElementById('jsonFile').files[0];
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = JSON.parse(e.target.result);
        updateDatabase(data);
    };

    reader.readAsText(file);
});

function updateDatabase(data) {
    try {
        // Attempt to stringify the data
        var jsonData = JSON.stringify(data);
        console.log('JSON Data to be sent:', jsonData);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_db.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Response from server:', xhr.responseText);
                alert('Database updated successfully!');
            } else if (xhr.readyState === 4) {
                console.error('Error updating database:', xhr.responseText);
            }
        };
        xhr.send(jsonData);
    } catch (e) {
        console.error('Error stringifying JSON:', e);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Fetch and populate product data
    fetch('get_products.php')
        .then(response => response.json())
        .then(products => {
            populateProductDropdown(products);
        })
        .catch(error => console.error('Error fetching products:', error));
});

function populateProductDropdown(products) {
    var productSelect = document.getElementById('taskProduct');

    products.forEach(product => {
        var option = document.createElement('option');
        option.value = product.id;
        option.textContent = product.name;
        option.dataset.category = product.category; // Store category in dataset
        option.dataset.quantity = product.quantity; // Store quantity in dataset
        productSelect.appendChild(option);
    });

    productSelect.addEventListener('change', function() {
        var selectedOption = productSelect.options[productSelect.selectedIndex];
        document.getElementById('taskQuantity').value = selectedOption.dataset.quantity;
        document.getElementById('taskCategory').value = selectedOption.dataset.category;
    });
}

document.getElementById('createTaskForm').addEventListener('submit', function(event) {
    event.preventDefault();

    var taskData = {
        product: document.getElementById('taskProduct').value,
        quantity: parseInt(document.getElementById('taskQuantity').value, 10),
        category: document.getElementById('taskCategory').value
    };

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'create_task.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                alert('Task created successfully!');
                // Optionally, update the rescue page by redirecting or fetching tasks again
                document.getElementById('taskMessage').textContent = "Task created successfully!";
            } else {
                console.error('Error creating task: ' + xhr.responseText);
                document.getElementById('taskMessage').textContent = "Error creating task!";
            }
        }
    };
    xhr.send(JSON.stringify(taskData));
});
