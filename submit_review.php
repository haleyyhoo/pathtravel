<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['route_id']) || !isset($_POST['rating']) || !isset($_POST['comment'])) {
    header("Location: routes.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$route_id = intval($_POST['route_id']);
$rating = intval($_POST['rating']);
$comment = trim($_POST['comment']);

if ($rating < 1 || $rating > 5 || empty($comment)) {
    header("Location: route.php?id=$route_id");
    exit;
}

$stmt = $db->prepare("INSERT INTO reviews (user_id, route_id, rating, comment) VALUES (:user_id, :route_id, :rating, :comment)");
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$stmt->bindValue(':route_id', $route_id, SQLITE3_INTEGER);
$stmt->bindValue(':rating', $rating, SQLITE3_INTEGER);
$stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
$stmt->execute();

header("Location: route.php?id=$route_id");
exit;
