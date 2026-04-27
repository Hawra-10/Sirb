<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $student_ID = $_SESSION['userID'];

    // Get form values
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $name       = $first_name . ' ' . $last_name;
    $major      = $_POST['major'];
    $skills_str = $_POST['skills'];

    // Update the student's name and major
    $stmt = $pdo->prepare("UPDATE Student SET name = ?, major = ? WHERE student_ID = ?");
    $stmt->execute([$name, $major, $student_ID]);

    // Delete old skills to avoid duplicates
    $stmt = $pdo->prepare("DELETE FROM studentskill WHERE student_ID = ?");
    $stmt->execute([$student_ID]);

    // Insert new skills
    if (!empty($skills_str)) {
        $skills = explode(',', $skills_str);

        foreach ($skills as $skill_name) {
            $skill_name = trim($skill_name);

            $stmt = $pdo->prepare("SELECT skill_ID FROM skill WHERE name = ?");
            $stmt->execute([$skill_name]);
            $skill = $stmt->fetch();

            if ($skill) {
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

        if (!empty($url)) {
            $stmt = $pdo->prepare("INSERT INTO pastproject (URL, courseName, student_ID) VALUES (?, ?, ?)");
            $stmt->execute([$url, $course, $student_ID]);
        }
    }

    header("Location: Homepage.php");
    exit();
}
?>