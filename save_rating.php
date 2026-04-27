<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You must login first.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$ratedStudentID = isset($data['ratedStudentID']) ? (int)$data['ratedStudentID'] : 0;
$rating = isset($data['rating']) ? (int)$data['rating'] : 0;
$tags = isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [];

$loggedUserID = (int)$_SESSION['userID'];

if ($ratedStudentID <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid rating data.'
    ]);
    exit;
}

if ($ratedStudentID === $loggedUserID) {
    echo json_encode([
        'success' => false,
        'message' => 'You cannot rate yourself.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $insertStmt = $pdo->prepare("
        INSERT INTO peerrate (starRate, Rated_student_ID)
        VALUES (?, ?)
    ");
    $insertStmt->execute([$rating, $ratedStudentID]);

    $rateID = $pdo->lastInsertId();

    foreach ($tags as $tagName) {
        $tagName = trim($tagName);

        if ($tagName === '') {
            continue;
        }

        $tagStmt = $pdo->prepare("
            SELECT tag_ID
            FROM ratetag
            WHERE name = ?
        ");
        $tagStmt->execute([$tagName]);
        $tag = $tagStmt->fetch();

        if ($tag) {
            $insertTagStmt = $pdo->prepare("
                INSERT INTO peerratetag (rate_ID, tag_ID)
                VALUES (?, ?)
            ");
            $insertTagStmt->execute([$rateID, $tag['tag_ID']]);
        }
    }

    $ratingStmt = $pdo->prepare("
        SELECT 
            AVG(starRate) AS average_rating,
            COUNT(rate_ID) AS review_count
        FROM peerrate
        WHERE Rated_student_ID = ?
    ");
    $ratingStmt->execute([$ratedStudentID]);
    $ratingData = $ratingStmt->fetch();

    $average = $ratingData['average_rating'] ? round($ratingData['average_rating'], 1) : 0;
    $count = $ratingData['review_count'] ? (int)$ratingData['review_count'] : 0;

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'average' => $average,
        'count' => $count
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>