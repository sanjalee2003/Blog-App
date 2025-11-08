<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'includes/session.php';

// Fetch all blog posts
$conn = getDBConnection();
$sql = "SELECT bp.*, u.username 
        FROM blogPost bp 
        JOIN user u ON bp.user_id = u.id 
        ORDER BY bp.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog App - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php">BlogApp</a>
            </div>
            <div class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <a href="create-blog.php" class="btn btn-primary">Create Blog</a>
                    <span>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">All Blog Posts</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="blog-grid">
                <?php while ($post = $result->fetch_assoc()): ?>
                    <div class="blog-card">
                        <h2><a href="view-blog.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        <div class="blog-meta">
                            <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                            <span><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <div class="blog-excerpt">
                            <?php 
                            $content = strip_tags($post['content']);
                            echo htmlspecialchars(substr($content, 0, 200)) . '...'; 
                            ?>
                        </div>
                        <a href="view-blog.php?id=<?php echo $post['id']; ?>" class="btn btn-link">Read More</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No blog posts yet. Be the first to create one!</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>