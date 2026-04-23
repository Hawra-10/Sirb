<?php
require 'db_connect.php';
$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['ids'];
if (!empty($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("DELETE FROM student WHERE student_ID IN ($placeholders)");
    $stmt->execute($ids);
    echo json_encode(['success' => true]);
}
?>