<?php
// Ξεκινάμε τη συνεδρία
session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['username'])) {
    header("Location: /web/pages/login/login.html");
    exit;
}

// Ελέγχουμε αν ο χρήστης έχει τον ρόλο του διαχειριστή
if ($_SESSION['role'] !== 'administrator') {
    echo "Access denied.";
    exit;
}
?>
