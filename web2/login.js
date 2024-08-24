document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting in the traditional way

    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'login.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Redirect based on user role
                if (response.role === 'administrator') {
                    window.location.href = 'admin_homepage.html';
                } else if (response.role === 'rescuer') {
                    window.location.href = 'rescuer_homepage.php';
                } else if (response.role === 'citizen') {
                    window.location.href = 'citizen_homepage.php';
                }
            } else {
                document.getElementById('error-message').textContent = response.message;
            }
        }
    };
    xhr.send('username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password));
});
