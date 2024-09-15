<?php
session_start();

// Αφήνουμε τα δεδομένα του χρήστη ανέπαφα, καθαρίζουμε μόνο τα συγκεκριμένα δεδομένα
if (isset($_SESSION['decoded_data'])) {
    unset($_SESSION['decoded_data']); // Καθαρίζει τα δεδομένα JSON που αποθηκεύτηκαν στη συνεδρία
}

// Ανακατεύθυνση στη σελίδα διαχείρισης ή άλλη σελίδα
header('Location: manage.html');
exit;
?>
