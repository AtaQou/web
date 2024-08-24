<?php
// Ξεκινάμε τη συνεδρία
session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['username'])) {
    // Ανακατεύθυνση στη σελίδα login αν δεν είναι συνδεδεμένος
    header("Location: /web/pages/login/login.html");
    exit;
}

// Ελέγχουμε αν ο χρήστης έχει τον ρόλο του citizen
if ($_SESSION['role'] !== 'citizen') {
    echo "Access denied.";
    exit;
}
?>
