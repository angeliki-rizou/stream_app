<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StreamApp Intro</title>
<style>
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
    color: white;
}


body {
    background: url('https://25.media.tumblr.com/38ba7aa23ce934325484372ae9e793f9/tumblr_mt9eusr6Tn1ryw6edo1_500.gif') no-repeat center center fixed;
    background-size: cover;
    position: relative;
    color: white;
}
body {
    padding-bottom: 60px; 
}
footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: black;
    color: white;
    text-align: center;
    padding: 15px;
    z-index: 100;
}


body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.32); 
    z-index: -1;
}

/* Navbar */
.navbar {
    background: black;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.navbar h1 {
    margin: 0;
    font-size: 22px;
    color: white;
    letter-spacing: 2px;
}
.navbar a {
    background: #00bfff;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    transition: 0.3s;
}
.navbar a:hover {
    background: #0099cc;
}


.intro-container {
    text-align: center;
    position: relative;
    top: 25%;
    max-width: 800px;
    margin: 0 auto;
}


.accordion {
    margin-top: 20px;
}

.accordion-header {
    background-color: rgba(255, 255, 255, 0.22);
    color: white;
    padding: 15px 20px;
    cursor: pointer;
    font-size: 18px;
    margin: 5px 0;
    border-radius: 5px;
    transition: 0.3s;
    text-align: left;
    position: relative;
}

.accordion-header:hover {
    background-color: rgba(255, 255, 255, 0.35);
}

.accordion-header::after {
    content: '\25BC'; 
    font-size: 14px;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    transition: transform 0.3s;
}

.accordion-header.active::after {
    transform: translateY(-50%) rotate(180deg);
}

.accordion-content {
    display: none;
    padding: 15px;
    background: rgba(0,0,0,0.6);
    border-radius: 0 0 8px 8px;
    margin-top: -5px;
    text-align: left;
}

.accordion-content p {
    margin: 0;
    font-size: 16px;
    line-height: 1.5;
}
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>StreamApp</h1>
    <a href="login.php">Σύνδεση</a>
</div>

<div class="intro-container">
    <h1>Καλώς ήρθατε στο <b>StreamApp</b> </h1>
    <p>Επιλέξτε μια ενότητα για να μάθετε περισσότερα:</p>

    <div class="accordion">
        <div class="accordion-header" onclick="toggleAccordion(this)">
            Σκοπός
        </div>
        <div class="accordion-content">
            <h3>Σκοπός</h3>
            <p>Σκοπός του ιστοτόπου είναι να επιτρέψει στους χρήστες να δημιουργήσουν δικές τους λίστες περιεχομένου ροής. 
            Επιπλέον, έχουν τη δυνατότητα να φτιάξουν δικά τους προφίλ, δημόσιες λίστες, αλλά και να ακολουθήσουν άλλους χρήστες.</p>
        </div>

        <div class="accordion-header" onclick="toggleAccordion(this)">
            Εγγραφή
        </div>
        <div class="accordion-content">
            <h3>Εγγραφή</h3>
            <p>Για να εγγραφείτε, πρέπει να <a href="register.php" style="color: #00ffff; text-decoration: underline;"><b>δημιουργήσετε λογαριασμό</b></a> με μοναδικό username και email.</p>
        </div>

        <div class="accordion-header" onclick="toggleAccordion(this)">
            Γιατί;
        </div>
        <div class="accordion-content">
            <h3>Γιατί;</h3>
            <p>Ο ιστότοπος προσφέρει βασικές λειτουργίες για τη δημιουργία λιστών περιεχομένου ροής. 
            Επιπλέον, δίνει τη δυνατότητα να ανακαλύπτετε νέο περιεχόμενο μέσω άλλων χρηστών και δημοσίων λιστών.</p>
        </div>

        <div class="accordion-header" onclick="toggleAccordion(this)">
            Βοήθεια
        </div>
        <div class="accordion-content">
             <h3><i class="fa fa-question-circle"></i> Σύντομη Βοήθεια</h3>
    <p>Το <b>StreamApp</b> σας επιτρέπει να δημιουργείτε λίστες περιεχομένου ροής και να ανακαλύπτετε νέο υλικό. 
    Ορίστε μερικές βασικές οδηγίες:</p>
    <ul>
        <li><i class="fa fa-user-plus"></i> <b>Εγγραφή</b>: Δημιουργήστε λογαριασμό με μοναδικό username και email.</li>
        <li><i class="fa fa-sign-in"></i> <b>Σύνδεση</b>: Συνδεθείτε για να αποκτήσετε πρόσβαση σε όλες τις λειτουργίες.</li>
        <li><i class="fa fa-list"></i> <b>Λίστες</b>: Δημιουργήστε, επεξεργαστείτε και μοιραστείτε τις λίστες σας.</li>
        <li><i class="fa fa-search"></i> <b>Αναζήτηση</b>: Βρείτε βίντεο από το YouTube και προσθέστε τα στις λίστες σας.</li>
        <li><i class="fa fa-users"></i> <b>Κοινότητα</b>: Ακολουθήστε άλλους χρήστες και δείτε ποιοι σας ακολουθούν.</li>
        <li><i class="fa fa-moon-o"></i> <b>Θέμα</b>: Αλλάξτε εμφάνιση (light/dark) από το μενού.</li>
    </ul>
        </div>
    </div>
</div>

<script>
function toggleAccordion(header) {
  const content = header.nextElementSibling;
  const all = document.querySelectorAll('.accordion-content');

  all.forEach(c => {
    if (c !== content) {
      c.style.display = "none";
      c.previousElementSibling.classList.remove('active');
    }
  });

  if (content.style.display === "block") {
    content.style.display = "none";
    header.classList.remove('active');
  } else {
    content.style.display = "block";
    header.classList.add('active');
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const firstHeader = document.querySelector('.accordion-header');
  if (firstHeader) {
    toggleAccordion(firstHeader);
  }
});
</script>

</body>
</html>
