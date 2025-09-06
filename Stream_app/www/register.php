<?php
session_start();

// Αν είναι ήδη συνδεδεμένος → dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Έλεγχος για theme από cookie
$theme = 'light'; 
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}

$page_title = "Εγγραφή - StreamApp";
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --bg-primary: #f5f5f5;
            --bg-secondary: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --accent-color: #2196F3;
            --border-color: #e0e0e0;
            --input-bg: #ffffff;
            --input-text: #333333;
        }

        [data-theme="dark"] {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --text-primary: #f1f1f1;
            --text-secondary: #aaaaaa;
            --accent-color: #0d47a1;
            --border-color: #333333;
            --input-bg: #2d2d2d;
            --input-text: #f1f1f1;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }

        .register-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 20px;
            background-color: var(--bg-secondary);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .w3-input {
            background-color: var(--input-bg);
            color: var(--input-text);
            border: 1px solid var(--border-color);
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .theme-toggle input { opacity: 0; width: 0; height: 0; }
        .theme-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .theme-slider:before {
            position: absolute;
            content: "";
            height: 26px; width: 26px;
            left: 4px; bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .theme-slider { background-color: var(--accent-color); }
        input:checked + .theme-slider:before { transform: translateX(26px); }
    </style>
</head>
<body data-theme="<?php echo $theme; ?>">

 <!-- Theme Toggle -->
    <div class="theme-toggle">
        <input type="checkbox" id="theme-switch" <?php echo $theme == 'dark' ? 'checked' : ''; ?>>
        <span class="theme-slider"></span>
    </div>
    <span style="position: absolute; top: 25px; right: 90px;">
        <i id="theme-icon" class="fa fa-<?php echo $theme == 'dark' ? 'moon-o' : 'sun-o'; ?>"></i>
    </span>

    <div class="register-container">
        <h2 class="w3-center"><i class="fa fa-user-plus"></i> Εγγραφή</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="w3-panel w3-red">
                <p><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="w3-panel w3-green">
                <p><?php echo htmlspecialchars($_GET['success']); ?></p>
            </div>
        <?php endif; ?>

        <form action="process_register.php" method="POST">
            <div class="w3-section">
                <label><b>Όνομα</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="text" name="first_name" placeholder="Εισάγετε το όνομα" required>

                <label><b>Επώνυμο</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="text" name="last_name" placeholder="Εισάγετε το επώνυμο" required>

                <label><b>Username</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="text" name="username" placeholder="Εισάγετε username" required>

                <label><b>Email</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="email" name="email" placeholder="Εισάγετε email" required>

                <label><b>Κωδικός</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="password" name="password" placeholder="Εισάγετε κωδικό" required>

                <label><b>Επιβεβαίωση Κωδικού</b></label>
                <input class="w3-input w3-border" type="password" name="password_confirm" placeholder="Ξαναγράψτε τον κωδικό" required>

                <button class="w3-button w3-block w3-blue w3-section w3-padding" type="submit">Εγγραφή</button>
            </div>
        </form>

        <div class="w3-center">
            <p>Έχετε ήδη λογαριασμό; <a href="login.php">Σύνδεση</a></p>
            <p><a href="intro.php">Επιστροφή στην αρχική σελίδα</a></p>
        </div>
    </div>

    <script>
          document.addEventListener('DOMContentLoaded', function() {
        const themeSwitch = document.getElementById('theme-switch');
        const themeIcon = document.getElementById('theme-icon');

        // όταν αλλάζει το toggle
        themeSwitch.addEventListener('change', function() {
            const theme = this.checked ? 'dark' : 'light';
            document.body.setAttribute('data-theme', theme);

            // αλλάζουμε το εικονίδιο
            themeIcon.className = this.checked ? 'fa fa-moon-o' : 'fa fa-sun-o';

            // αποθήκευση σε cookie
            document.cookie = "theme=" + theme + "; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
        });
        
    });

    </script>
</body>
</html>
