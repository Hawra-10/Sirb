<?php
session_start();

require 'db_connect.php';
require_once 'rating_helper.php';

// 1. Ensure the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

// 2. Get the logged-in user's ID
$loggedUserID = $_SESSION['userID'];

// 3. Fetch the user's profile information
$stmt = $pdo->prepare("
    SELECT student_ID, name, major, email
    FROM student
    WHERE student_ID = ?
");
$stmt->execute([$loggedUserID]);
$user = $stmt->fetch();

if (!$user) {
    die("User profile not found.");
}

// 4. Fetch the user's skills
$skillsStmt = $pdo->prepare("
    SELECT skill.name
    FROM studentskill
    JOIN skill ON studentskill.skill_ID = skill.skill_ID
    WHERE studentskill.student_ID = ?
");
$skillsStmt->execute([$loggedUserID]);
$skills = $skillsStmt->fetchAll();

// 5. Fetch the user's past projects
$projectsStmt = $pdo->prepare("
    SELECT courseName, URL
    FROM pastproject
    WHERE student_ID = ?
");
$projectsStmt->execute([$loggedUserID]);
$projects = $projectsStmt->fetchAll();

// 6. Fetch Ratings and Tags
$rating = getStudentRating($pdo, $loggedUserID);
$tagCounts = getStudentTagCounts($pdo, $loggedUserID);

// Helper functions
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

$average = $rating['average'] ?? 0;
$count = $rating['count'] ?? 0;
$degree = ($average / 5) * 360;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Profile</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
        <style>
            .edit-input {
                font-family: 'DM Sans', sans-serif;
                font-size: 14px;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                padding: 4px 8px;
                width: 100%;
                background: #f8fafc;
                outline-color: #10b981;
                margin-bottom: 8px;
            }
            #available-skills-grid .bd-peer-skill-tag {
                cursor: pointer;
                transition: all 0.2s ease;
                user-select: none;
            }
            .limit-reached, .url-error {
                color: #ef4444;
                font-size: 11px;
                margin-top: 5px;
                display: none;
            }
            .project-input-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
                margin-top: 15px;
                padding: 15px;
                background: #f1f5f9;
                border-radius: 8px;
                border: 1px dashed #cbd5e1;
            }
            .project-link-icon {
                font-size: 12px;
                color: #10b981;
                text-decoration: none;
                margin-left: auto;
                font-weight: bold;
            }
            .project-link-icon:hover {
                text-decoration: underline;
            }
        </style>
    </head>

    <body id="bd-body-peer-profile">

        <div class="drawer-overlay" id="overlay" onclick="closeDrawer()"></div>

        <div class="drawer" id="drawer">
            <div class="drawer-profile">
                <a href="profile.php" class="profile-avatar">👤</a>
                <div>
                    <div class="profile-name">
                        <a class="profile-name" href="profile.php"><?= h($user['name']) ?></a>
                    </div>
                    <div class="profile-role"><?= h($user['major']) ?></div>
                </div>
            </div>

            <a class="drawer-logout" href="login.php">
                ← Log out
            </a>
        </div>

        <main id="bd-peer-profile-page">
            <section id="bd-peer-profile-shell">
                <div id="bd-peer-top-glow"></div>

                <section id="bd-peer-profile-card">
                    <header id="bd-peer-profile-header">
                        <a id="bd-peer-back-button" href="HomePage.php">←</a>
                        <h1 id="bd-peer-profile-title">My Profile</h1>
                        <button class="hamburger" id="bd-peer-hamburger" onclick="toggleDrawer()" aria-label="Menu">
                            <span></span><span></span><span></span>
                        </button>
                    </header>

                    <section id="bd-peer-profile-top-section">
                        <section id="bd-peer-profile-left-side">
                            <div id="bd-peer-avatar"><?= initials($user['name']) ?></div>

                            <div class="bd-peer-info-row">
                                <span class="bd-peer-info-label">Name</span>
                                <span id="name-container"><span class="bd-peer-info-value" id="bd-peer-name"><?= h($user['name']) ?></span></span>
                            </div>

                            <div class="bd-peer-info-row">
                                <span class="bd-peer-info-label">Major</span>
                                <span id="major-container"><span class="bd-peer-info-value" id="bd-peer-major"><?= h($user['major']) ?></span></span>
                            </div>

                            <div class="bd-peer-info-row">
                                <span class="bd-peer-info-label">Email</span>
                                <span id="email-container"><span class="bd-peer-info-value" id="bd-peer-email"><?= h($user['email']) ?></span></span>
                            </div>
                        </section>

                        <section id="bd-peer-profile-right-side">
                            <div id="bd-peer-rating-section">
                                <div id="bd-peer-rating-circle" style="background: conic-gradient(#10b981 <?= $degree ?>deg, #e2e8f0 0deg);">
                                    <span id="bd-peer-rating-number"><?= number_format($average, 1) ?></span>
                                </div>
                                <div id="bd-peer-rating-details">
                                    <p id="bd-peer-rating-stars">★★★★★</p>
                                    <p id="bd-peer-rating-reviews">Your Average Rating (<?= $count ?> Reviews)</p>
                                </div>
                            </div>
                        </section>
                    </section>

                    <section id="bd-peer-skills-section">
                        <h2 id="bd-peer-skills-title">What others say</h2>
                        <div id="bd-peer-skills-list">
                            <?php if (!empty($tagCounts)): ?>
                                <?php foreach ($tagCounts as $tag): ?>
                                    <span class="bd-peer-skill-tag <?= skillClass($tag['name']) ?>">
                                        <?= h($tag['name']) ?> (<?= h($tag['count'] ?? 1) ?>)
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="font-size: 13px; color: #64748b;">No peer ratings yet.</p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section id="bd-peer-skills-section-2">
                        <h2 id="bd-peer-skills-title-2">My Skills</h2>
                        <div id="bd-peer-skills-list-2">
                            <?php if (!empty($skills)): ?>
                                <?php foreach ($skills as $skill): ?>
                                    <span class="bd-peer-skill-tag <?= skillClass($skill['name']) ?>"><?= h($skill['name']) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="font-size: 13px; color: #64748b;">No skills added yet.</p>
                            <?php endif; ?>
                        </div>

                        <div id="edit-skills-container" style="display: none; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                            <p style="font-weight: bold; font-size: 13px; color: #475569; margin-bottom: 5px; letter-spacing: 0.5px;">SELECT SKILLS (MAX 3) *</p>
                            <p id="skill-warning" class="limit-reached">Maximum of 3 skills allowed!</p>
                            <div id="available-skills-grid" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;"></div>
                        </div>
                    </section>

                    <section id="bd-peer-projects-section">
                        <h2 id="bd-peer-projects-title">My Projects</h2>
                        <div id="bd-peer-projects-list">
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $project): ?>
                                    <div class="bd-peer-project-item">
                                        <span class="bd-peer-project-line"></span>
                                        <span class="bd-peer-project-text"><?= h($project['courseName']) ?></span>
                                        <a href="<?= h($project['URL']) ?>" target="_blank" class="project-link-icon">🔗 View</a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="font-size: 13px; color: #64748b;">No projects added yet.</p>
                            <?php endif; ?>
                        </div>

                        <div id="add-project-ui" style="display: none;">
                            <div class="project-input-group">
                                <p style="font-weight: bold; font-size: 11px; color: #64748b; margin-bottom: 5px;">ADD NEW PROJECT</p>
                                <input type="text" id="new-project-name" class="edit-input" placeholder="Project Name (e.g. My App)">
                                <input type="text" id="new-project-url" class="edit-input" placeholder="Google Drive or Docs Link">
                                <p id="url-error-msg" class="url-error">Invalid link. Please use a Google Drive or Docs URL.</p>
                                <button onclick="addNewProject()" style="background: #1e293b; color: white; border: none; padding: 8px; border-radius: 5px; cursor: pointer; font-size: 12px;">+ Add to List</button>
                            </div>
                        </div>
                    </section>


                    <div id="bd-peer-rate-button-wrapper">
                        <button id="bd-peer-rate-button" onclick="toggleEditMode()">Edit Profile</button>
                    </div>

                    <div id="bd-peer-delete-button-wrapper" >
                        <button id="bd-peer-delete-button" onclick="confirmDeleteAccount()" >
                            Delete Account
                        </button>
                    </div>
                </section>
            </section>
        </main>

        <footer>
            © 2026 <span>Sirb</span>. All rights reserved.
        </footer>

        <script>
            function confirmDeleteAccount() {
                const confirmation = confirm("Are you absolutely sure you want to delete your account? This action cannot be undone and you will lose all your data.");

                if (confirmation) {
                    // Redirect to a PHP file that handles the actual database deletion and session destruction
                    window.location.href = 'delete_account.php';
                }
            }
            function toggleDrawer() {
                document.getElementById('drawer').classList.toggle('active');
                document.getElementById('overlay').classList.toggle('active');
            }

            function closeDrawer() {
                document.getElementById('drawer').classList.remove('active');
                document.getElementById('overlay').classList.remove('active');
            }

            let isEditMode = false;
            const availableSkills = ["Leadership", "Research", "Innovation", "Communication", "Problem Solving", "Programming", "Teamwork", "Data Analysis", "Coding", "UI/UX"];

            function toggleEditMode() {
                const btn = document.getElementById('bd-peer-rate-button');
                const skillContainer = document.getElementById('edit-skills-container');
                const projectUI = document.getElementById('add-project-ui');

                // Mapping the display spans to their container IDs
                const fields = [
                    {id: 'bd-peer-name', parent: 'name-container', key: 'name'},
                    {id: 'bd-peer-major', parent: 'major-container', key: 'major'},
                    {id: 'bd-peer-email', parent: 'email-container', key: 'email'}
                ];

                if (!isEditMode) {
                    // --- ENTER EDIT MODE ---
                    isEditMode = true;
                    btn.innerText = "Save Changes";
                    btn.style.backgroundColor = "#10b981";
                    skillContainer.style.display = "block";
                    projectUI.style.display = "block";

                    fields.forEach(field => {
                        const element = document.getElementById(field.id);
                        const parent = document.getElementById(field.parent);
                        const val = element.innerText;
                        parent.innerHTML = `<input type="text" id="${field.id}" class="edit-input" value="${val}">`;
                    });

                    renderSkillPicker();

                } else {
                    // --- SAVE & EXIT EDIT MODE ---

                    // 1. Collect Basic Info
                    const updatedData = {
                        name: document.getElementById('bd-peer-name').value,
                        major: document.getElementById('bd-peer-major').value,
                        email: document.getElementById('bd-peer-email').value,
                        skills: [],
                        projects: []
                    };

                    // 2. Collect Skills (Targeting the FIXED ID)
                    const skillTags = document.querySelectorAll('#bd-peer-skills-list-2 .bd-peer-skill-tag');
                    updatedData.skills = Array.from(skillTags).map(tag => {
                        return tag.innerText.trim();
                    });

                    // 3. Collect Projects
                    const projectItems = document.querySelectorAll('.bd-peer-project-item');
                    updatedData.projects = Array.from(projectItems).map(item => {
                        return {
                            courseName: item.querySelector('.bd-peer-project-text').innerText,
                            url: item.querySelector('a').getAttribute('href')
                        };
                    });

                    // 4. Send Data to PHP via Fetch API
                    fetch('update_profile.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(updatedData)
                    })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // UI Cleanup: Revert inputs back to spans
                                    fields.forEach(field => {
                                        const input = document.getElementById(field.id);
                                        const parent = document.getElementById(field.parent);
                                        const newVal = input.value;
                                        parent.innerHTML = `<span class="bd-peer-info-value" id="${field.id}">${newVal}</span>`;
                                    });

                                    // Hide edit panels
                                    isEditMode = false;
                                    btn.innerText = "Edit Profile";
                                    btn.style.backgroundColor = "";
                                    skillContainer.style.display = "none";
                                    projectUI.style.display = "none";

                                } else {
                                    alert("Failed to save: " + (data.message || "Unknown error"));
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert("A server error occurred while saving.");
                            });
                }
            }

            function renderSkillPicker() {
                const grid = document.getElementById('available-skills-grid');
                // Get current skills targeting the FIXED ID
                const currentSkills = Array.from(document.querySelectorAll('#bd-peer-skills-list-2 .bd-peer-skill-tag'))
                        .map(s => s.childNodes[0].textContent.trim());

                grid.innerHTML = '';

                availableSkills.forEach(name => {
                    const tag = document.createElement('span');
                    tag.className = 'bd-peer-skill-tag';
                    tag.innerText = name;

                    const isActive = currentSkills.includes(name);

                    tag.style.opacity = isActive ? "1" : "0.35";
                    tag.style.border = isActive ? "2px solid #1e293b" : "1px dashed #cbd5e1";
                    tag.style.fontWeight = isActive ? "bold" : "normal";
                    tag.style.cursor = "pointer";

                    tag.onclick = () => {
                        toggleSkill(name);
                        renderSkillPicker();
                    };
                    grid.appendChild(tag);
                });
            }

            function toggleSkill(name) {
                // Target the FIXED ID
                const list = document.getElementById('bd-peer-skills-list-2');
                const warning = document.getElementById('skill-warning');

                const currentTags = Array.from(list.querySelectorAll('.bd-peer-skill-tag'));
                const existingTag = currentTags.find(t => t.childNodes[0].textContent.trim() === name);

                if (existingTag) {
                    existingTag.remove();
                    warning.style.display = "none";

                    if (list.querySelectorAll('.bd-peer-skill-tag').length === 0) {
                        list.innerHTML = '<p style="font-size: 13px; color: #64748b;">No skills added yet.</p>';
                    }
                } else {
                    const emptyMsg = list.querySelector('p');
                    if (emptyMsg)
                        emptyMsg.remove();

                    if (currentTags.length >= 3) {
                        warning.style.display = "block";
                        return;
                    }

                    warning.style.display = "none";
                    const newTag = document.createElement('span');

                    let colorClass = 'teal';
                    const lowerName = name.toLowerCase();
                    if (lowerName.includes('coding') || lowerName.includes('programming'))
                        colorClass = 'navy';
                    else if (lowerName.includes('ui') || lowerName.includes('innovation'))
                        colorClass = 'pink';
                    else if (lowerName.includes('research') || lowerName.includes('data'))
                        colorClass = 'gold';

                    newTag.className = `bd-peer-skill-tag bd-peer-skill-tag-${colorClass}`;
                    newTag.innerText = name;
                    list.appendChild(newTag);
                }
            }

            function addNewProject() {
                const nameInput = document.getElementById('new-project-name');
                const urlInput = document.getElementById('new-project-url');
                const errorMsg = document.getElementById('url-error-msg');

                const name = nameInput.value.trim();
                const url = urlInput.value.trim();

                const googleRegex = /^(https?:\/\/)?(docs\.google\.com|drive\.google\.com)\/.*$/;

                if (name === "" || !googleRegex.test(url)) {
                    errorMsg.style.display = "block";
                    return;
                }

                errorMsg.style.display = "none";

                const list = document.getElementById('bd-peer-projects-list');
                const newItem = document.createElement('div');
                newItem.className = 'bd-peer-project-item';
                newItem.innerHTML = `
                  <span class="bd-peer-project-line"></span>
                  <span class="bd-peer-project-text">${finalName}</span>
                  <a href="${url}" target="_blank" class="project-link-icon" style="display:none;">🔗 View</a>
                    `;

                list.appendChild(newItem);

                nameInput.value = "";
                urlInput.value = "";
            }
        </script>
        <script src="script.js"></script>
    </body>
</html>