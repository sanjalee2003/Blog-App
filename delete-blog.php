<?php
require_once 'config/database.php';
require_once 'includes/session.php';

requireLogin();

$blog_id = $_GET['id'] ?? 0;
$user_id = getCurrentUserId();

$conn = getDBConnection();

// Delete blog post (only if user is the owner)
$stmt = $conn->prepare("DELETE FROM blogPost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['message'] = 'Blog post deleted successfully';
} else {
    $_SESSION['message'] = 'Failed to delete blog post or unauthorized';
}

$stmt->close();
$conn->close();

header('Location: index.php');
exit();
?>