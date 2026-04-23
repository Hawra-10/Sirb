<?php

include 'db_connect.php';
require_once 'rating_helper.php';

$search = isset($_GET['student_name']) ? trim($_GET['student_name']) : "";
$major = isset($_GET['major']) ? $_GET['major'] : "all";

$sql = "SELECT * FROM Student WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND name LIKE :name";
    $params['name'] = "%$search%";
}

if ($major !== "all" && !empty($major)) {
    $sql .= " AND major = :major";
    $params['major'] = $major;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$html = "";
if (count($students) > 0) {
    foreach ($students as $row) {
        $name = htmlspecialchars($row["name"]);
        $majorStr = htmlspecialchars($row["major"]);
        $id = $row["student_ID"];
        
        $rating = getStudentRating($pdo, $id);
        $tags = getTopStudentTags($pdo, $id);
        $parts = explode(' ', $name);
        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

        $tagHtml = "";
        if (!empty($tags)) {
            foreach ($tags as $t) {
                $cls = getTagClass($t);
                $tagHtml .= "<span class='tag $cls'>" . htmlspecialchars($t) . "</span>";
            }
        } else {
            $tagHtml = "<span class='tag' style='opacity: 0.5;'>No tags yet</span>";
        }

        $html .= "
        <a href='PeerProfile.php?id=$id' class='student-card'>
            <div class='card-avatar'>$initials</div>
            <div class='card-name'>$name</div>
            <div class='card-major'>$majorStr</div>
            <div class='card-stars'>" . renderStars($rating['average']) . " 
                <span style='font-size: 11px; color: var(--muted);'>({$rating['count']})</span>
            </div>
            <div class='tag-row'>$tagHtml</div>
        </a>";
    }
} else {
    $html = "<div class='empty'><div class='empty-icon'>🔍</div><p>No students found.</p></div>";
}

header('Content-Type: application/json');
echo json_encode(['html' => $html, 'count' => count($students)]);
