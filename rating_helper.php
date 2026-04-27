<?php
/**
 * Calculates the average rating and review count for a student
 * @param PDO $pdo The database connection
 * @param int $student_id The ID of the student to calculate for
 * @return array ['average' => float, 'count' => int]
 */
function getStudentRating($pdo, $student_id) {
    $sql = "SELECT 
                AVG(starRate) as average_rating, 
                COUNT(rate_ID) as review_count 
            FROM peerrate 
            WHERE Rated_student_ID = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $student_id]);
    $data = $stmt->fetch();

    return [
        // round to 1 decimal place, default to 0 if null
        'average' => $data['average_rating'] ? round($data['average_rating'], 1) : 0,
        'count'   => $data['review_count'] ? (int)$data['review_count'] : 0
    ];
}

/**
 * Renders the HTML stars based on a numeric rating
 */
function renderStars($rating) {
    $output = "";
    $filledStars = round($rating);
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $filledStars) {
            $output .= '<span class="star filled">★</span>';
        } else {
            $output .= '<span class="star">★</span>';
        }
    }
    return $output;
}


/**
 * Retrieves the tags with the highest count for a student
 * @param PDO $pdo 
 * @param int $student_id
 * @return array List of tag names
 */
function getTopStudentTags($pdo, $student_id) {
    // This query counts tags per student and only returns those equal to the MAX count
    $sql = "SELECT rt.name, COUNT(prt.tag_ID) as tag_count
            FROM ratetag rt
            JOIN peerratetag prt ON rt.tag_ID = prt.tag_ID
            JOIN peerrate pr ON prt.rate_ID = pr.rate_ID
            WHERE pr.Rated_student_ID = :id
            GROUP BY rt.tag_ID
            HAVING tag_count = (
                SELECT COUNT(prt2.tag_ID) as max_count
                FROM peerratetag prt2
                JOIN peerrate pr2 ON prt2.rate_ID = pr2.rate_ID
                WHERE pr2.Rated_student_ID = :id2
                GROUP BY prt2.tag_ID
                ORDER BY max_count DESC
                LIMIT 1
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $student_id, 'id2' => $student_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns simple array of names like ['Coding', 'Teamwork']
}

/**
 * Maps the Tag Name to the correct CSS class
 */
function getTagClass($tagName) {
    $map = [
        'Leadership'    => 'leadership',
        'Coding'        => 'coding',
        'Research'      => 'research',
        'UI/UX'         => 'uiux',
        'Communication' => 'communication',
        'Teamwork'      => 'teamwork'
    ];
    // Return the class if it exists, otherwise return a default 'tag' class
    return $map[$tagName] ?? 'tag-default';
}