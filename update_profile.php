<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userID = $_SESSION['userID'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->beginTransaction();

    // 1. Update Student Table
    $stmt = $pdo->prepare("UPDATE student SET name = ?, major = ?, email = ? WHERE student_ID = ?");
    $stmt->execute([$data['name'], $data['major'], $data['email'], $userID]);

    // 2. Sync Skills (Delete old ones first)
    $pdo->prepare("DELETE FROM studentskill WHERE student_ID = ?")->execute([$userID]);
    foreach ($data['skills'] as $skillName) {
        // Find the ID for the skill name
        $sStmt = $pdo->prepare("SELECT skill_ID FROM skill WHERE name = ?");
        $sStmt->execute([$skillName]);
        $skill = $sStmt->fetch();
        if ($skill) {
            $pdo->prepare("INSERT INTO studentskill (student_ID, skill_ID) VALUES (?, ?)")
                ->execute([$userID, $skill['skill_ID']]);
        }
    }

    // 3. Sync Projects
    $pdo->prepare("DELETE FROM pastproject WHERE student_ID = ?")->execute([$userID]);
    foreach ($data['projects'] as $proj) {
        $pdo->prepare("INSERT INTO pastproject (student_ID, courseName, URL) VALUES (?, ?, ?)")
            ->execute([$userID, $proj['name'], $proj['url']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>