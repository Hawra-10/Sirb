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


//  Capture inputs from the GET form
$search_query_raw = isset($_GET['student_name']) ? trim($_GET['student_name']) : "";
$selected_major = isset($_GET['major']) ? $_GET['major'] : "all";

// Base Query Logic
$sql_query = "SELECT * FROM Student WHERE 1=1";
$params = [];

// Add Name Filter 
if (!empty($search_query_raw)) {
    $sql_query .= " AND name LIKE :name";
    $params['name'] = "%" . $search_query_raw . "%";
}

// Add Major Filter
if ($selected_major !== "all" && !empty($selected_major)) {
    $sql_query .= " AND major = :major";
    $params['major'] = $selected_major;
}

// Execute the Query
$stmt = $pdo->prepare($sql_query);
$stmt->execute($params);
$result = $stmt->fetchAll(); // Gets all matching students
$studentCount = count($result);
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

                        <select name="major" onchange="this.form.submit()">
                            <option value="all" <?php echo ($selected_major == 'all') ? 'selected' : ''; ?>>All</option>
                            <option value="IT" <?php echo ($selected_major == 'IT') ? 'selected' : ''; ?>>IT</option>
                            <option value="CS" <?php echo ($selected_major == 'CS') ? 'selected' : ''; ?>>CS</option>
                            <option value="IS" <?php echo ($selected_major == 'IS') ? 'selected' : ''; ?>>IS</option>
                            <option value="SWE" <?php echo ($selected_major == 'SWE') ? 'selected' : ''; ?>>SWE</option>
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
                    <span id="countNum"><?php echo $studentCount; ?></span> Students
                </div>

                <?php if ($selected_major !== 'all' || !empty($search_query)): ?>
                    <a href="homepage.php" class="clear-filters">Clear All ✕</a>
                <?php endif; ?>
            </div>

            <!-- CARD GRID -->

            <div class="card-grid" id="cardGrid">


                <?php
                // Check if results exist
                if ($studentCount > 0) {
                    // Loop through each student record
                    foreach ($result as $row) {
                        $name = htmlspecialchars($row["name"]);
                        $major = htmlspecialchars($row["major"]);
                        $student_id = $row["student_ID"];
                        $ratingData = getStudentRating($pdo, $row['student_ID']);
                        $average = $ratingData['average'];
                        $count = $ratingData['count'];
                        $topTags = getTopStudentTags($pdo, $row['student_ID']);

                        // Generate Initials for the Avatar
                        $parts = explode(' ', $name);
                        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

                        // Display the Card
                        echo "
        <a href='PeerProfile.php?id={$student_id}' class='student-card'>
            <div class='card-avatar'>{$initials}</div>
            <div class='card-name'>{$name}</div>
            <div class='card-major'>{$major}</div>
            <div class='card-stars'>
        " . renderStars($average) . "
        <span style='font-size: 11px; color: var(--muted); margin-left: 4px;'>({$count})</span>
    </div>
            <div class='tag-row'>
    <?php if (!empty($topTags)): ?>
        <?php foreach ($topTags as $tagName): ?>
            <span class='tag <?php echo getTagClass($tagName); ?>'>
                <?php echo htmlspecialchars($tagName); ?>
            </span>
        <?php endforeach; ?>
    <?php else: ?>
        <span class='tag' style='opacity: 0.5;'>No tags yet</span>
    <?php endif; ?>
</div>
        </a>";
                    }
                } else {
                    echo "
        <div class='empty'>
            <div class='empty-icon'>🔍</div>
            <p>No students found matching your criteria.</p>
        </div>";
                }
                ?>
            </div>

        </div>
        <!-- ─── FOOTER ─── -->
        <footer>
            © 2025 <span>Sirb</span>. All rights reserved.
        </footer>

        <script src="script.js"></script>
    </body>

</html>