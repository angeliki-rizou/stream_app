<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: protected.php");
    exit;
}

$theme = 'light';
if (!empty($_COOKIE['theme']) && in_array($_COOKIE['theme'], ['light','dark'], true)) {
    $theme = $_COOKIE['theme'];
}

$page_title = "Σύνδεση - StreamApp";
?>
<!DOCTYPE html>
<html lang="el" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        /* ---- THEME TOKENS ---- */
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
        html[data-theme="dark"] {
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
            transition: background-color 0.25s, color 0.25s;
        }

        .login-container {
            max-width: 420px;
            margin: 100px auto;
            padding: 24px;
            background-color: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .w3-input {
            background-color: var(--input-bg);
            color: var(--input-text);
            border: 1px solid var(--border-color);
        }

        /* ---- THEME TOGGLE SWITCH ---- */
        .theme-toggle-wrap {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 56px;
            height: 30px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #bbb;
            transition: .3s;
            border-radius: 30px;
        }
        .slider:before {
            content: "";
            position: absolute;
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: #fff;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.25);
        }
        input:checked + .slider { background-color: var(--accent-color); }
        input:checked + .slider:before { transform: translateX(26px); }
    </style>
</head>
<body>

    <!-- Theme Toggle -->
    <div class="theme-toggle-wrap" aria-label="Εναλλαγή θέματος">
        <i id="theme-icon" class="fa <?php echo $theme === 'dark' ? 'fa-moon-o' : 'fa-sun-o'; ?>"
           aria-hidden="true" title="Theme"></i>

        <label class="switch" for="theme-switch">
            <input type="checkbox" id="theme-switch" <?php echo $theme === 'dark' ? 'checked' : ''; ?>>
            <span class="slider"></span>
        </label>
    </div>

    <div class="login-container">
        <h2 class="w3-center" style="margin-top:0">
            <i class="fa fa-sign-in"></i> Σύνδεση
        </h2>

        <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
        <div class="w3-panel w3-red">
            <p>Λάθος όνομα χρήστη ή κωδικός πρόσβασης.</p>
        </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST" novalidate>
            <div class="w3-section">
                <label><b>Όνομα χρήστη</b></label>
                <input class="w3-input w3-border w3-margin-bottom" type="text"
                       placeholder="Εισάγετε το όνομα χρήστη" name="username" required>

                <label><b>Κωδικός πρόσβασης</b></label>
                <input class="w3-input w3-border" type="password"
                       placeholder="Εισάγετε τον κωδικό πρόσβασης" name="password" required>

                <button class="w3-button w3-block w3-blue w3-section w3-padding" type="submit">
                    Σύνδεση
                </button>
            </div>
        </form>

        <div class="w3-center">
            <p>Δεν έχετε λογαριασμό; <a href="register.php">Εγγραφή</a></p>
            <p><a href="intro.php">Επιστροφή στην αρχική σελίδα</a></p>
        </div>
    </div>

    <script>
    (function () {
        const htmlEl = document.documentElement;
        const checkbox = document.getElementById('theme-switch');
        const icon = document.getElementById('theme-icon');

        function setTheme(theme) {
            htmlEl.setAttribute('data-theme', theme);
            icon.className = 'fa ' + (theme === 'dark' ? 'fa-moon-o' : 'fa-sun-o');
            document.cookie = "theme=" + theme + "; max-age=31536000; path=/; samesite=lax";
        }

        checkbox.addEventListener('change', function () {
            setTheme(this.checked ? 'dark' : 'light');
        });


        const initialTheme = htmlEl.getAttribute('data-theme') || 'light';
        checkbox.checked = (initialTheme === 'dark');
    })();
    </script>
</body>
</html>
