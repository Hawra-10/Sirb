<?php
session_start();

include 'db_connect.php';
require_once 'rating_helper.php';

// check if logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's data
$logged_user_ID = $_SESSION['userID'];

$userQuery = "SELECT name, major FROM Student WHERE student_ID = :id";
$userStmt = $pdo->prepare($userQuery);
$userStmt->execute(['id' => $logged_user_ID]);
$currentUser = $userStmt->fetch();

//  Fallback if user is not found 
if (!$currentUser) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$currentUserName = htmlspecialchars($currentUser['name']);
$currentUserMajor = htmlspecialchars($currentUser['major']);
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Sirb – Homepage</title>
        <link
            href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
            rel="stylesheet" />
        <link rel="stylesheet" href="style.css">
    </head>

    <body id="home-bd">

        <!-- DRAWER OVERLAY -->
        <div class="drawer-overlay" id="overlay" onclick="closeDrawer()"></div>

        <!-- SIDE DRAWER -->
        <div class="drawer" id="drawer">
            <div class="drawer-profile">
                <a href="profile.php" class="profile-avatar">👤</a>
                <div>
                    <div class="profile-name">
                        <a class="profile-name" href="profile.php"><?php echo $currentUserName; ?></a>
                    </div>
                    <div class="profile-role"><?php echo $currentUserMajor; ?></div>
                </div>
            </div>

            <a class="drawer-logout" href="login.php">
                ← Log out
            </a>
        </div>

        <!-- MAIN -->
        <div class="main" id="home_main">

            <!-- TOP BAR -->
            <div class="topbar">
                <!-- Logo -->
                <div class="logo-wrap">
                    <img src="logo.png" width="48">
                    <span class="logo-name">Sirb</span>
                </div>

                <!-- Search + Filter -->
                <form action="" method="GET" class="search-bar">
                    <input type="text" name="student_name" placeholder="Search by name…" />

                    <div class="filter-btn">
                        <span class="filter-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="6" x2="20" y2="6"></line>
                            <line x1="7" y1="12" x2="17" y2="12"></line>
                            <line x1="10" y1="18" x2="14" y2="18"></line>
                            </svg>
                        </span>

                        <span class="filter-text">Filter <span class="chevron">▾</span></span>

                        <select name="major" id="majorSelect">
                            <option value="all" selected>All</option>
                            <option value="IT">IT</option>
                            <option value="CS">CS</option>
                            <option value="IS">IS</option>
                            <option value="SWE">SWE</option>
                        </select>
                    </div>
                </form>

                <!-- Hamburger -->
                <button class="hamburger" id="hamburger" onclick="toggleDrawer()" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <!-- RESULTS META -->
            <div class="results-meta">
                <div class="results-count">
                    <span id="countNum">0</span> Students
                </div>

                <button type="button" id="clearBtn" class="clear-filters" style="display: none; background: none; border: none; cursor: pointer;">
                    Clear All ✕
                </button>
            </div>
        </div>

        <!-- CARD GRID -->

        <div class="card-grid" id="cardGrid">
            <div class="empty">
                <p>Loading students...</p>
            </div>
        </div>

    </div>
    
    
    <!-- ─── FOOTER ─── -->
    <footer>
        © 2025 <span>Sirb</span>. All rights reserved.
    </footer>

    <script src="script.js"></script>
</body>

</html>