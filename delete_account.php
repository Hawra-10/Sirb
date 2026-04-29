<?php
session_start();
require 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

$loggedUserID = $_SESSION['userID'];

try {
    // Start a transaction
    $pdo->beginTransaction();

    // 1. Delete peer ratings where this student is the one being rated.
    // This is required because the `peerrate` table lacks ON DELETE CASCADE.
    $stmtRatings = $pdo->prepare("DELETE FROM peerrate WHERE Rated_student_ID = ?");
    $stmtRatings->execute([$loggedUserID]);

    // 2. Delete linked records from other tables
    // (Your DB has ON DELETE CASCADE for these, but executing them manually inside 
    // the transaction is a safe redundancy)
    $stmtSkills = $pdo->prepare("DELETE FROM studentskill WHERE student_ID = ?");
    $stmtSkills->execute([$loggedUserID]);

    $stmtProjects = $pdo->prepare("DELETE FROM pastproject WHERE student_ID = ?");
    $stmtProjects->execute([$loggedUserID]);

    // 3. Finally, delete the user from the main student table
    $stmtUser = $pdo->prepare("DELETE FROM student WHERE student_ID = ?");
    $stmtUser->execute([$loggedUserID]);

    // Commit the changes to the database
    $pdo->commit();

    // 4. Destroy the session and log them out
    session_unset();
    session_destroy();

    // Redirect to login
    header("Location: splash.php?message=Account+Deleted");
    exit;

} catch (PDOException $e) {
    // If something goes wrong, roll back the database
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    die("General Error: " . $e->getMessage());
}
?>