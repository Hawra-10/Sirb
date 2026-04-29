<?php
session_start();

require 'db_connect.php';
require_once 'rating_helper.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$loggedUserID = $_SESSION['userID'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: Homepage.php");
    exit;
}

$peerID = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT student_ID, name, major, email
    FROM Student
    WHERE student_ID = ?
");
$stmt->execute([$peerID]);
$peer = $stmt->fetch();

if (!$peer) {
    die("Student not found.");
}

$currentStmt = $pdo->prepare("
    SELECT name, major
    FROM Student
    WHERE student_ID = ?
");
$currentStmt->execute([$loggedUserID]);
$currentUser = $currentStmt->fetch();

$currentUserName = $currentUser ? htmlspecialchars($currentUser['name']) : "User";
$currentUserMajor = $currentUser ? htmlspecialchars($currentUser['major']) : "";

$skillsStmt = $pdo->prepare("
    SELECT Skill.name
    FROM StudentSkill
    JOIN Skill ON StudentSkill.skill_ID = Skill.skill_ID
    WHERE StudentSkill.student_ID = ?
");
$skillsStmt->execute([$peerID]);
$skills = $skillsStmt->fetchAll();

$projectsStmt = $pdo->prepare("
    SELECT courseName, URL
    FROM PastProject
    WHERE student_ID = ?
");
$projectsStmt->execute([$peerID]);
$projects = $projectsStmt->fetchAll();

$rating = getStudentRating($pdo, $peerID);

// ✅ التعديل هنا فقط
$tagCounts = getStudentTagCounts($pdo, $peerID);

function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function initials($name) {
    $parts = preg_split('/\s+/', trim($name));

    if (count($parts) === 1) {
        return strtoupper(substr($parts[0], 0, 2));
    }

    return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
}

function skillClass($skillName) {
    $name = strtolower($skillName);

    if (str_contains($name, 'coding') || str_contains($name, 'programming')) {
        return 'bd-peer-skill-tag-navy';
    }

    if (str_contains($name, 'research') || str_contains($name, 'data')) {
        return 'bd-peer-skill-tag-gold';
    }

    if (str_contains($name, 'ui') || str_contains($name, 'ux') || str_contains($name, 'innovation')) {
        return 'bd-peer-skill-tag-pink';
    }

    return 'bd-peer-skill-tag-teal';
}

$average = $rating['average'];
$count = $rating['count'];
$degree = ($average / 5) * 360;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Peer Profile</title>

        <link rel="stylesheet" href="style.css">

        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    </head>

    <body id="bd-body-peer-profile">

        <input type="hidden" id="rated-student-id" value="<?php echo $peerID; ?>">

        <div class="drawer-overlay" id="overlay" onclick="closeDrawer()"></div>

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

            <a class="drawer-logout" href="splash.php">
                ← Log out
            </a>
        </div>

        <div id="bd-rate-modal-overlay"></div>

        <div id="bd-rate-modal">
            <div id="bd-rate-modal-card">

                <h2 id="bd-rate-modal-title">Rate Peer</h2>

                <div id="bd-rate-stars-row">
                    <button type="button" class="bd-rate-star" data-value="1">★</button>
                    <button type="button" class="bd-rate-star" data-value="2">★</button>
                    <button type="button" class="bd-rate-star" data-value="3">★</button>
                    <button type="button" class="bd-rate-star" data-value="4">★</button>
                    <button type="button" class="bd-rate-star" data-value="5">★</button>
                </div>

                <div id="bd-rate-tags-title">Choose tags:</div>

                <div id="bd-rate-tags-list">
                    <button type="button" class="bd-rate-tag-option" data-tag="Leadership">Leadership</button>
                    <button type="button" class="bd-rate-tag-option" data-tag="Research">Research</button>
                    <button type="button" class="bd-rate-tag-option" data-tag="Coding">Coding</button>
                    <button type="button" class="bd-rate-tag-option" data-tag="UI/UX">UI/UX</button>
                    <button type="button" class="bd-rate-tag-option" data-tag="Teamwork">Teamwork</button>
                    <button type="button" class="bd-rate-tag-option" data-tag="Communication">Communication</button>
                </div>

                <div id="bd-rate-actions">
                    <button type="button" id="bd-rate-cancel-button">Cancel</button>
                    <button type="button" id="bd-rate-submit-button">Submit</button>
                </div>

            </div>
        </div>

        <div id="bd-rate-success-toast">Rating submitted successfully!</div>

        <main id="bd-peer-profile-page">

            <section id="bd-peer-profile-shell">

                <div id="bd-peer-top-glow"></div>

                <section id="bd-peer-profile-card">

                    <header id="bd-peer-profile-header">
                        <a id="bd-peer-back-button" href="Homepage.php">←</a>
                        <h1 id="bd-peer-profile-title">Peer Profile</h1>

                        <button class="hamburger" id="bd-peer-hamburger" onclick="toggleDrawer()" aria-label="Menu">
                            <span></span><span></span><span></span>
                        </button>
                    </header>

                    <section id="bd-peer-profile-top-section">

                        <section id="bd-peer-profile-left-side">

                            <div id="bd-peer-avatar"><?php echo initials($peer['name']); ?></div>

                            <div class="bd-peer-info-row">
                                <span class="bd-peer-info-label">Name</span>
                                <span class="bd-peer-info-value" id="bd-peer-name">
                                    <?php echo h($peer['name']); ?>
                                </span>
                            </div>

                            <div class="bd-peer-info-row">
                                <span class="bd-peer-info-label">Major</span>
                                <span class="bd-peer-info-value" id="bd-peer-major">
                                    <?php echo h($peer['major']); ?>
                                </span>
                            </div>

                            <div class="bd-peer-info-row">
                                <span class="bd-peer-info-label">Email</span>
                                <span class="bd-peer-info-value" id="bd-peer-email">
                                    <a href="mailto:<?php echo h($peer['email']); ?>" class="peer-email-link">
                                        <?php echo h($peer['email']); ?>
                                    </a>
                                </span>
                            </div>

                        </section>

                        <section id="bd-peer-profile-right-side">

                            <div id="bd-peer-rating-section">

                                <div id="bd-peer-rating-circle"
                                     style="background: conic-gradient(var(--teal) 0deg <?php echo $degree; ?>deg, #e6eef5 <?php echo $degree; ?>deg 360deg);">
                                    <span id="bd-peer-rating-number">
                                        <?php echo number_format($average, 1); ?>
                                    </span>
                                </div>

                                <div id="bd-peer-rating-details">
                                    <p id="bd-peer-rating-stars">
                                        <?php echo renderStars($average); ?>
                                    </p>
                                    <p id="bd-peer-rating-reviews">
                                        <?php echo $count; ?> reviews
                                    </p>
                                </div>

                            </div>

                        </section>

                    </section>

                    <section id="bd-peer-skills-section">

                        <h2 id="bd-peer-skills-title">What others say</h2>

                        <div id="bd-peer-skills-list">
                            <?php if (!empty($tagCounts)): ?>
                                <?php foreach ($tagCounts as $index => $tag): ?>
                                    <span class="bd-peer-skill-tag <?php echo skillClass($tag['name']); ?> <?php echo $index === 0 ? 'bd-peer-top-tag' : ''; ?>">
                                        <?php echo h($tag['name']); ?>
                                        <span class="bd-peer-tag-count">(<?php echo $tag['tag_count']; ?>)</span>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="bd-peer-skill-tag bd-peer-skill-tag-none">
                                    No tags yet
                                </span>
                            <?php endif; ?>
                        </div>

                    </section>

                    <section id="bd-peer-skills-section-2">

                        <h2 id="bd-peer-skills-title-2">Skills</h2>

                        <div id="bd-peer-skills-list-2">
                            <?php if (!empty($skills)): ?>
                                <?php foreach ($skills as $skill): ?>
                                    <span class="bd-peer-skill-tag">
                                        <?php echo h($skill['name']); ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="bd-peer-skill-tag">No skills added yet</span>
                            <?php endif; ?>
                        </div>

                    </section>

                    <section id="bd-peer-projects-section">

                        <h2 id="bd-peer-projects-title">Projects</h2>

                        <div id="bd-peer-projects-list">
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    <div class="bd-peer-project-item">
                                        <span class="bd-peer-project-line"></span>

                                        <?php
                                        $url = $project['URL'] ?? '';
                                        // Check if name is null, empty, or just whitespace
                                        $name = trim($project['courseName'] ?? '');

                                        // If name is empty, use the placeholder "Project" (or whatever you prefer)
                                        $linkText = (!empty($name)) ? $name : "Project";
                                        ?>

                                        <?php if (!empty($url)): ?>
                                            <a class="bd-peer-project-text"
                                               href="<?php echo h($url); ?>"
                                               target="_blank">
                                                   <?php echo h($linkText); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="bd-peer-project-text">
                                                <?php echo h($linkText); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="bd-peer-project-item">
                                    <span class="bd-peer-project-line"></span>
                                    <span class="bd-peer-project-text">No projects added yet</span>
                                </div>
                            <?php endif; ?>
                        </div>

                    </section>

                    <?php if ($loggedUserID != $peerID): ?>
                        <div id="bd-peer-rate-button-wrapper">
                            <button id="bd-peer-rate-button">Rate</button>
                        </div>
                    <?php endif; ?>

                </section>

            </section>

        </main>

        <footer>
            © 2025 <span>Sirb</span>. All rights reserved.
        </footer>

        <script src="script.js"></script>
    </body>
</html>