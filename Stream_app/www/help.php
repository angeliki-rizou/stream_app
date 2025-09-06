<?php
session_start();

// Αν δεν είναι συνδεδεμένος, πήγαινε στο login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$page_title = "Βοήθεια - StreamApp";

ob_start();
?>
<style>
.help-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
}
.help-section {
    margin-bottom: 25px;
}
.help-section h3 {
    color: #2196F3;
    margin-bottom: 10px;
}
.help-section p {
    font-size: 16px;
    line-height: 1.6;
}
.help-section ul {
    margin-top: 10px;
}
</style>

<div class="help-container">
    <h2><i class="fa fa-question-circle"></i> Βοήθεια & Οδηγός Χρήσης</h2>
    <p>Καλώς ήρθατε στο <b>StreamApp</b>! Εδώ θα βρείτε έναν σύντομο οδηγό για τις βασικές λειτουργίες της εφαρμογής.</p>
    <hr>

    <div class="help-section">
        <h3><i class="fa fa-user-plus"></i> Εγγραφή</h3>
        <p>Για να ξεκινήσετε, χρειάζεται να <b>δημιουργήσετε λογαριασμό</b> με μοναδικό όνομα χρήστη (username) και email.</p>
    </div>

    <div class="help-section">
        <h3><i class="fa fa-sign-in"></i> Σύνδεση</h3>
        <p>Αν έχετε ήδη λογαριασμό, μπορείτε να συνδεθείτε μέσω της σελίδας <b>Σύνδεση</b> με το username και τον κωδικό πρόσβασης.</p>
    </div>

    <div class="help-section">
        <h3><i class="fa fa-list"></i> Λίστες</h3>
        <p>Μπορείτε να δημιουργήσετε προσωπικές λίστες περιεχομένου και να προσθέσετε βίντεο από το YouTube.</p>
        <ul>
            <li>Δημιουργία νέας λίστας</li>
            <li>Προσθήκη περιεχομένου σε λίστα</li>
            <li>Διαχείριση λιστών (επεξεργασία, διαγραφή)</li>
        </ul>
    </div>

    <div class="help-section">
        <h3><i class="fa fa-search"></i> Αναζήτηση</h3>
        <p>Μέσα από την ενότητα <b>Αναζήτηση YouTube</b> μπορείτε να βρείτε βίντεο και να τα προσθέσετε στις λίστες σας.</p>
    </div>

    <div class="help-section">
        <h3><i class="fa fa-users"></i> Κοινότητα</h3>
        <p>Μπορείτε να ακολουθήσετε άλλους χρήστες και να δείτε ποιοι σας ακολουθούν. Έτσι ανακαλύπτετε νέο περιεχόμενο.</p>
    </div>

    <div class="help-section">
        <h3><i class="fa fa-moon-o"></i> Θέμα εμφάνισης</h3>
        <p>Από το κουμπί <b>Εναλλαγής Θέματος</b> στο μενού μπορείτε να αλλάξετε το θέμα σε <i>dark mode</i> ή <i>light mode</i>.</p>
    </div>

    <div class="help-section">
        <h3><i class="fa fa-database"></i> Open Data</h3>
        <p>Οι λίστες σας μπορούν να εξαχθούν σε μορφή <b>YAML</b> ώστε να χρησιμοποιηθούν ως ανοικτά δεδομένα.</p>
    </div>

    <hr>
    <p class="w3-center"><a href="protected.php" class="w3-button w3-blue"><i class="fa fa-arrow-left"></i> Επιστροφή στο Dashboard</a></p>
</div>

<?php
$content = ob_get_clean();
include("layout.php");
