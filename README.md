Disaster Relief Web Application
===============================

Overview
--------
This project is a disaster relief management system with three user roles: Administrator, Rescuer, and Citizen.
It uses PHP for the backend and MySQL for the database. PHP runs via XAMPP, while MySQL runs in Docker.

Requirements
------------
- XAMPP (Apache + PHP)
- Docker Desktop (for MySQL)
- Modern web browser

Note: Both XAMPP and Docker must run simultaneously for the system to work.

Setup Instructions
------------------

1. Install XAMPP
   - Mac: https://www.apachefriends.org
     Install and start Apache.
   - Windows: https://www.apachefriends.org
     Start Apache from XAMPP Control Panel.

2. Install Docker Desktop
   - Mac: https://www.docker.com/products/docker-desktop/
   - Windows: https://www.docker.com/products/docker-desktop/
   Make sure Docker is running.

3. Clone the Repository
   - Mac:
     cd /Applications/XAMPP/htdocs
     git clone git@github.com:YourUsername/web.git
     cd web

   - Windows:
     cd C:\xampp\htdocs
     git clone git@github.com:YourUsername\web.git
     cd web

4. Start Docker MySQL
   The project includes docker-compose.yml for the database. Run:

     docker compose up -d

   - Mac: Ensure /Applications/XAMPP/xamppfiles/htdocs/web/db/data is shared in Docker Desktop → Preferences → Resources → File Sharing.
   - Windows: Ensure C:\xampp\htdocs\web\db\data is shared in Docker Desktop.

   MySQL will run on port 3310 (host: 127.0.0.1).

5. Configure PHP Database Connection
   Check includes/db_connect.php to ensure host, port, username, and password match Docker MySQL:

     $servername = "127.0.0.1";
     $port = 3310;
     $username = "root";
     $password = "root";
     $dbname = "web";

6. Initialize the Database
   If this is the first run, load the schema and sample data:

     docker exec -i web-mysql mysql -uroot -proot < db_init.txt

   This creates all tables and adds sample users, vehicles, and inventory.

7. Run the Application
   Open your browser and go to:

     http://localhost/web

Sample Accounts
---------------
Username   | Password | Role
-----------|---------|---------------
admin1     | 1234    | administrator
citizen1   | abcd    | citizen
citizen2   | abcd    | citizen
rescuer1   | pass    | rescuer
rescuer2   | pass    | rescuer

Troubleshooting
---------------
- Docker volume issues: Make sure the db/data folder is shared in Docker Desktop.
- PHP errors: Check XAMPP logs:
  - Mac: /Applications/XAMPP/xamppfiles/logs/error_log
  - Windows: C:\xampp\htdocs\web\logs\php_error_log
- Database connection: Ensure container is running:

     docker ps

  Restart Docker/MySQL if needed.

Notes
-----
- Do not modify any existing PHP or SQL files; just follow the steps above.
- Both XAMPP and Docker must be running simultaneously to use the application.
