<?php
// Start the session at the very beginning of the file
session_start(); 

// Assuming you have a file that establishes the $pdo connection
// require 'db.php'; 
include 'db_connect.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Basic Validation
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // 2. Check if email already exists in the database
        $stmt = $pdo->prepare("SELECT * FROM student WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "This email is already registered.";
        } else {
            // --- PASSWORD HASHING START ---
            // PASSWORD_DEFAULT uses the strongest current algorithm (currently BCrypt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // --- PASSWORD HASHING END ---

            $_SESSION['pending_email'] = $email;
            
            // Store the HASHED version, not the plain text one!
            $_SESSION['pending_password'] = $hashed_password;
            header("Location: setupProfile.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>

<body class="ar-body-login">
  <div class="ar-body-inner">
    <div class="ar-container-login">
      <div class="ar-brand">
        <div class="ar-brand-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5z" /><path d="M2 17l10 5 10-5" /><path d="M2 12l10 5 10-5" />
          </svg>
        </div>
      </div>

      <h1 class="ar-title">Register</h1>
      <p class="ar-subtitle">Create your account to get started.</p>

      <?php if ($error): ?>
        <div class="ar-error ar-visible" style="margin-bottom: 15px; text-align: center;">
            <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form action="register.php" method="POST" id="registerForm">
        <div class="ar-field">
          <label class="ar-label" for="ar-email">Email</label>
          <div class="ar-input-wrap">
            <svg class="ar-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
            </svg>
            <input id="ar-email" name="email" class="ar-input" type="email" placeholder="Example@gmail.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
          </div>
          <div class="ar-error" id="ar-email-error">Please enter a valid email address.</div>
        </div>

        <div class="ar-field">
          <label class="ar-label" for="ar-password">Password</label>
          <div class="ar-input-wrap">
            <svg class="ar-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <input id="ar-password" name="password" class="ar-input" type="password" placeholder="••••••••" />
            <button class="ar-toggle-pw" id="ar-toggle-pw" type="button" tabindex="-1">
              <svg class="ar-eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
          <div class="ar-error" id="ar-password-error">Password must be at least 6 characters.</div>
        </div>

        <div class="ar-field">
          <label class="ar-label" for="ar-password-confirm">Confirm Password</label>
          <div class="ar-input-wrap">
            <svg class="ar-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <input id="ar-password-confirm" name="confirm_password" class="ar-input" type="password" placeholder="••••••••" />
            <button class="ar-toggle-pw" id="ar-toggle-confirm" type="button" tabindex="-1">
              <svg class="ar-eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
          <div class="ar-error" id="ar-confirm-error">Passwords do not match.</div>
        </div>

        <button type="submit" class="ar-button" id="ar-register-btn">Register</button>
        
        <div class="ar-signup-prompt">
        Already have an account? <a href="login.php">Sign in here</a>
      </div>
      </form>
    </div>
  </div>

  <footer class="ar-footer">
    &copy; 2025 <span>Sirb</span>. All rights reserved.
  </footer>

  <script>
    const form = document.getElementById('registerForm');
    const registerBtn = document.getElementById('ar-register-btn');
    const emailInput = document.getElementById('ar-email');
    const passwordInput = document.getElementById('ar-password');
    const confirmInput = document.getElementById('ar-password-confirm');
    
    // Error Elements
    const emailError = document.getElementById('ar-email-error');
    const passwordError = document.getElementById('ar-password-error');
    const confirmError = document.getElementById('ar-confirm-error');

    // Helper function to show/hide errors
    function toggleError(element, show) {
        if (show) {
            element.classList.add('ar-visible');
            element.style.display = 'block'; // Ensure it's visible if CSS uses display:none
        } else {
            element.classList.remove('ar-visible');
            element.style.display = 'none';
        }
    }

    form.addEventListener('submit', (e) => {
        let valid = true;

        // 1. Email Validation (Simple regex)
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailInput.value)) {
            toggleError(emailError, true);
            valid = false;
        } else {
            toggleError(emailError, false);
        }

        // 2. Password Length Validation
        if (passwordInput.value.length < 6) {
            toggleError(passwordError, true);
            valid = false;
        } else {
            toggleError(passwordError, false);
        }

        // 3. Confirm Password Validation
        if (passwordInput.value !== confirmInput.value) {
            toggleError(confirmError, true);
            valid = false;
        } else {
            toggleError(confirmError, false);
        }
        
        if (!valid) {
            e.preventDefault(); // Stop form submission
        } else {
            registerBtn.disabled = true;
            registerBtn.textContent = 'Creating Account...';
        }
    });

    // --- Keep your existing Toggle Eye Logic below ---
    const togglePw = document.getElementById('ar-toggle-pw');
    const toggleConfirm = document.getElementById('ar-toggle-confirm');
    const EYE_OPEN = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    const EYE_SHUT = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

    function setupPasswordToggle(button, input) {
      button.addEventListener('click', () => {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.querySelector('.ar-eye-icon').innerHTML = isHidden ? EYE_SHUT : EYE_OPEN;
      });
    }
    setupPasswordToggle(togglePw, passwordInput);
    setupPasswordToggle(toggleConfirm, confirmInput);
</script>
</body>
</html>
