<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the logged-in student's email from session
    $email = $_SESSION['email'];

    // Get form values
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $name       = $first_name . ' ' . $last_name;
    $major      = $_POST['major'];
    $skills_str = $_POST['skills']; // comma-separated e.g. "Programming,Teamwork"

    // Get the student_ID using their email
    $stmt = $pdo->prepare("SELECT student_ID FROM Student WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch();
  if (!$student) {
    $_SESSION['error'] = "Account not found. Please sign up.";
    header("Location: signup.php");
    exit();
  }
    $student_ID = $student['student_ID'];

    // Update the student's name and major
    $stmt = $pdo->prepare("UPDATE Student SET name = ?, major = ? WHERE student_ID = ?");
    $stmt->execute([$name, $major, $student_ID]);

    // Delete old skills to avoid duplicates
    $stmt = $pdo->prepare("DELETE FROM studentskill WHERE student_ID = ?");
    $stmt->execute([$student_ID]);

    // Insert new skills
    if (!empty($skills_str)) {
        $skills = explode(',', $skills_str); // turn the string into an array

        foreach ($skills as $skill_name) {
            $skill_name = trim($skill_name);

            // Find the skill_ID from the skill table
            $stmt = $pdo->prepare("SELECT skill_ID FROM skill WHERE name = ?");
            $stmt->execute([$skill_name]);
            $skill = $stmt->fetch();

            if ($skill) {
                // Insert into studentskill
                $stmt = $pdo->prepare("INSERT INTO studentskill (student_ID, skill_ID) VALUES (?, ?)");
                $stmt->execute([$student_ID, $skill['skill_ID']]);
            }
        }
    }

    // Insert projects
    $project_names = $_POST['project_name'] ?? [];
    $project_urls  = $_POST['project_url']  ?? [];

    for ($i = 0; $i < count($project_urls); $i++) {
        $url  = trim($project_urls[$i]);
        $course = trim($project_names[$i]);

        // Only insert if URL is not empty
        if (!empty($url)) {
            $stmt = $pdo->prepare("INSERT INTO pastproject (URL, courseName, student_ID) VALUES (?, ?, ?)");
            $stmt->execute([$url, $course, $student_ID]);
        }
    }

    // Redirect to homepage after saving
    header("Location: Homepage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Set up Profile</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link
    href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="ar-body-setup">
<div class="arS-container">

  <h1 class="ar-title">Set up Profile</h1>

  <form method="POST" action="" id="ar-setup-form">

    <!-- Hidden input that holds the selected skills as a comma-separated string -->
    <input type="hidden" name="skills" id="ar-skills-input" value="" />

    <!-- Name -->
    <div class="ar-field">
      <label class="ar-label">Name <span class="ar-required">*</span></label>
      <div class="ar-name-row">
        <div class="ar-name-col">
          <input id="ar-firstname-input" class="ar-input" type="text" name="first_name" placeholder="First name" autocomplete="off" />
          <div class="ar-error" id="ar-firstname-error">First name is required.</div>
        </div>
        <div class="ar-name-col">
          <input id="ar-lastname-input" class="ar-input" type="text" name="last_name" placeholder="Last name" autocomplete="off" />
          <div class="ar-error" id="ar-lastname-error">Last name is required.</div>
        </div>
      </div>
    </div>

    <!-- Major -->
    <div class="ar-field">
      <label class="ar-label">Major <span class="ar-required">*</span></label>
      <div class="ar-radio-group" id="ar-major-group">
        <label class="ar-radio-option">
          <input type="radio" name="major" value="Computer Science" />
          <span class="ar-radio-label">Computer Science</span>
        </label>
        <label class="ar-radio-option">
          <input type="radio" name="major" value="Information Technology" />
          <span class="ar-radio-label">Information Technology</span>
        </label>
        <label class="ar-radio-option">
          <input type="radio" name="major" value="Information Systems" />
          <span class="ar-radio-label">Information Systems</span>
        </label>
        <label class="ar-radio-option">
          <input type="radio" name="major" value="Software Engineering" />
          <span class="ar-radio-label">Software Engineering</span>
        </label>
      </div>
      <div class="ar-error" id="ar-major-error">Please select a major.</div>
    </div>

    <!-- Skills -->
    <div class="ar-field">
      <label class="ar-label">Skills <span class="ar-required">*</span></label>
      <div class="ar-skills-wrap" id="ar-skills-wrap">
        <div class="ar-skill" data-skill="Leadership">Leadership <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Research">Research <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Innovation">Innovation <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Communication">Communication <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Problem Solving">Problem Solving <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Programming">Programming <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Teamwork">Teamwork <span class="ar-skill-check">✓</span></div>
        <div class="ar-skill" data-skill="Data Analysis">Data Analysis <span class="ar-skill-check">✓</span></div>
      </div>
      <p class="ar-skills-hint">Select up to 3 skills</p>
      <div class="ar-error" id="ar-skills-error">Please select at least one skill.</div>
    </div>

    <div class="ar-divider"></div>

    <!-- Projects -->
    <div class="ar-field">
      <label class="ar-label">Projects</label>
      <div id="ar-projects-list" class="ar-projects-list"></div>
    </div>

    <!-- Done button -->
    <button class="ar-button" type="submit" id="ar-done-btn">Done</button>
    <div class="ar-success-msg" id="ar-success">🎉 Profile set up successfully!</div>

  </form>

</div>

<footer class="ar-footer">
  © 2025 <span>Sirb</span>. All rights reserved.
</footer>

<script>
  // ── Skills ──
  const selectedSkills = new Set();
  const MAX_SKILLS = 3;

  document.querySelectorAll('.ar-skill').forEach(chip => {
    chip.addEventListener('click', () => {
      const skill = chip.dataset.skill;
      if (chip.classList.contains('ar-skill-selected')) {
        chip.classList.remove('ar-skill-selected');
        selectedSkills.delete(skill);
      } else {
        if (selectedSkills.size >= MAX_SKILLS) return;
        chip.classList.add('ar-skill-selected');
        selectedSkills.add(skill);
      }
      updateDisabled();
    });
  });

  function updateDisabled() {
    document.querySelectorAll('.ar-skill').forEach(chip => {
      if (!chip.classList.contains('ar-skill-selected') && selectedSkills.size >= MAX_SKILLS) {
        chip.classList.add('ar-skill-disabled');
      } else {
        chip.classList.remove('ar-skill-disabled');
      }
    });
  }

  // ── Projects ──
  let projectCount = 0;
  const list = document.getElementById('ar-projects-list');

  function isValidGoogleUrl(url) {
    try {
      const u = new URL(url);
      return u.hostname === 'drive.google.com' || u.hostname === 'docs.google.com';
    } catch { return false; }
  }

  function addProjectCard() {
    projectCount++;
    const idx = projectCount;
    const card = document.createElement('div');
    card.className = 'ar-project-card';
    card.dataset.idx = idx;
    card.innerHTML = `
      <div class="ar-project-card-header">
        <span class="ar-project-num">Project ${idx}</span>
        ${idx > 1 ? '<button type="button" class="ar-remove-btn" title="Remove">✕</button>' : ''}
      </div>
      <div>
        <span class="ar-project-sublabel">Subject Name</span>
        <input class="ar-input ar-proj-name" type="text" name="project_name[]" placeholder="e.g. Practical Software Engineering" autocomplete="off" />
      </div>
      <div>
        <span class="ar-project-sublabel">Project URL</span>
        <input class="ar-input ar-proj-url" type="url" name="project_url[]" placeholder="https://drive.google.com/..." autocomplete="off" />
        <div class="ar-error ar-proj-url-error">Please provide a valid Google Drive or Google Docs sharing link.</div>
        <p class="ar-url-note">Make sure the link permissions are set to 'Public' or 'Anyone with the link'</p>
      </div>
    `;

    const removeBtn = card.querySelector('.ar-remove-btn');
    if (removeBtn) {
      removeBtn.addEventListener('click', () => {
        card.classList.add('ar-card-removing');
        setTimeout(() => { card.remove(); renumberCards(); }, 280);
      });
    }

    const nameInput = card.querySelector('.ar-proj-name');
    const urlInput  = card.querySelector('.ar-proj-url');

    function checkAutoAdd() {
      const isLast = card === list.lastElementChild;
      if (isLast && nameInput.value.trim() && urlInput.value.trim()) {
        addProjectCard();
      }
    }

    nameInput.addEventListener('input', checkAutoAdd);
    urlInput.addEventListener('input', () => {
      urlInput.classList.remove('ar-error-input');
      card.querySelector('.ar-proj-url-error').classList.remove('ar-visible');
      checkAutoAdd();
    });

    list.appendChild(card);
    requestAnimationFrame(() => card.classList.add('ar-card-in'));
  }

  function renumberCards() {
    list.querySelectorAll('.ar-project-card').forEach((card, i) => {
      card.querySelector('.ar-project-num').textContent = 'Project ' + (i + 1);
    });
  }

  addProjectCard();

  // ── Submit ──
  document.getElementById('ar-setup-form').addEventListener('submit', (e) => {
    e.preventDefault();
    let valid = true;

    function showError(id, show) {
      document.getElementById(id).classList.toggle('ar-visible', show);
    }

    const firstName = document.getElementById('ar-firstname-input').value.trim();
    if (!firstName) {
      showError('ar-firstname-error', true);
      document.getElementById('ar-firstname-input').classList.add('ar-error-input');
      valid = false;
    } else {
      showError('ar-firstname-error', false);
      document.getElementById('ar-firstname-input').classList.remove('ar-error-input');
    }

    const lastName = document.getElementById('ar-lastname-input').value.trim();
    if (!lastName) {
      showError('ar-lastname-error', true);
      document.getElementById('ar-lastname-input').classList.add('ar-error-input');
      valid = false;
    } else {
      showError('ar-lastname-error', false);
      document.getElementById('ar-lastname-input').classList.remove('ar-error-input');
    }

    const major = document.querySelector('input[name="major"]:checked');
    if (!major) {
      showError('ar-major-error', true);
      valid = false;
    } else {
      showError('ar-major-error', false);
    }

    if (selectedSkills.size === 0) {
      showError('ar-skills-error', true);
      valid = false;
    } else {
      showError('ar-skills-error', false);
    }

    // Validate project URLs
    list.querySelectorAll('.ar-project-card').forEach(card => {
      const urlInput = card.querySelector('.ar-proj-url');
      const urlErr   = card.querySelector('.ar-proj-url-error');
      const urlVal   = urlInput.value.trim();
      if (urlVal && !isValidGoogleUrl(urlVal)) {
        urlErr.classList.add('ar-visible');
        urlInput.classList.add('ar-error-input');
        valid = false;
      } else {
        urlErr.classList.remove('ar-visible');
        urlInput.classList.remove('ar-error-input');
      }
    });

    if (valid) {
      // Put selected skills into the hidden input before submitting
      document.getElementById('ar-skills-input').value = [...selectedSkills].join(',');

      document.getElementById('ar-success').classList.add('ar-visible');
      document.getElementById('ar-done-btn').disabled = true;

      // Submit the form to PHP
      e.target.submit();
    }
  });

  ['ar-firstname-input', 'ar-lastname-input'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
      document.getElementById(id).classList.remove('ar-error-input');
    });
  });
</script>

</body>
</html>