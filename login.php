<?php
session_start();

$error = "";
$old_email = "";

// check if there is a message from another page
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $old_email = $email;

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {

        // Connect to the database
        require_once 'db_connect.php';

        // Search for the student by email
        $stmt = $pdo->prepare("SELECT * FROM Student WHERE email = ?");
        $stmt->execute([$email]);
        $student = $stmt->fetch();

        $stmt2 = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt2->execute([$email]);
        $admin = $stmt2->fetch();

        // Check if student exists and verify hashed password
        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['email'] = $student['email'];
            $_SESSION['userID'] = $student['student_ID'] ?? $student['id'] ?? null;

            header("Location: Homepage.php");
            exit();
        }
        // Check if admin exists and verify hashed password
        else if ($admin && password_verify($password, $admin['password'])) {
            // Optional: You might want to set an admin session ID here too
            $_SESSION['email'] = $admin['email'];
            header("Location: admin_maintenance.php");
            exit();
        } else {
            $error = "Wrong email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Login</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
            rel="stylesheet" />
        <link rel="stylesheet" href="style.css" />
        <style>
            .ar-alert {
                background: rgba(239, 68, 68, 0.08);
                border: 1px solid rgba(239, 68, 68, 0.35);
                border-radius: 10px;
                padding: 12px 14px;
                margin-bottom: 20px;
                color: #ef4444;
                font-size: 0.84rem;
                font-family: 'DM Sans', sans-serif;
            }
        </style>
    </head>

    <body class="ar-body-login">

        <div class="ar-body-inner">
            <div class="ar-container-login">

                <div class="ar-brand">
                    <div class="ar-brand-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"
                             stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5" />
                        <path d="M2 12l10 5 10-5" />
                        </svg>
                    </div>
                </div>

                <h1 class="ar-title">Login</h1>
                <p class="ar-subtitle">Welcome back! Sign in to continue.</p>

                <!-- Show error message if login failed -->
                <?php if ($error != ""): ?>
                    <div class="ar-alert"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">

                    <div class="ar-field">
                        <label class="ar-label" for="ar-email">Email</label>
                        <div class="ar-input-wrap">
                            <svg class="ar-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input id="ar-email" class="ar-input" type="email" name="email"
                                   placeholder="you@example.com" value="<?= htmlspecialchars($old_email) ?>" />
                        </div>
                    </div>

                    <div class="ar-field">
                        <label class="ar-label" for="ar-password">Password</label>
                        <div class="ar-input-wrap">
                            <svg class="ar-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input id="ar-password" class="ar-input" type="password" name="password" placeholder="••••••••" />
                            <button class="ar-toggle-pw" id="ar-toggle-pw" type="button" tabindex="-1" aria-label="Toggle password">
                                <svg id="ar-eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <button class="ar-button" type="submit">Login</button>
                    </form>

                    <div class="ar-signup-prompt">
                        Don't have an account? <a href="register.php">Register here</a>
                    </div>
                </form>

            </div>
        </div>

        <footer class="ar-footer">
            &copy; 2025 <span>Sirb</span>. All rights reserved.
        </footer>

        <script>
            // Show/hide password
            const togglePw = document.getElementById('ar-toggle-pw');
            const eyeIcon = document.getElementById('ar-eye-icon');
            const passwordInput = document.getElementById('ar-password');

            const EYE_OPEN = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
            const EYE_SHUT = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

            togglePw.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                eyeIcon.innerHTML = isHidden ? EYE_SHUT : EYE_OPEN;
            });
        </script>

    </body>
</html>