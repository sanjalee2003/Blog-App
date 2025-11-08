<?php
require_once 'config/database.php';
require_once 'includes/session.php';

$blog_id = $_GET['id'] ?? 0;

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT bp.*, u.username 
                        FROM blogPost bp 
                        JOIN user u ON bp.user_id = u.id 
                        WHERE bp.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$post = $result->fetch_assoc();
$is_owner = isLoggedIn() && $post['user_id'] == getCurrentUserId();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Blog App</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
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
        <article class="blog-single">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="blog-meta">
                <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                <span>Published: <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                <?php if ($post['updated_at'] != $post['created_at']): ?>
                    <span>Updated: <?php echo date('M d, Y', strtotime($post['updated_at'])); ?></span>
                <?php endif; ?>
            </div>

            <?php if ($is_owner): ?>
                <div class="blog-actions">
                    <a href="edit-blog.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Edit</a>
                    <a href="delete-blog.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this blog post?')">Delete</a>
                </div>
            <?php endif; ?>
            
            <div class="blog-content" id="blog-content">
                <?php echo $post['content']; ?>
            </div>
        </article>
        
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </div>

    <script>
        // Render markdown if content contains markdown
        const content = document.getElementById('blog-content');
        const rawContent = content.textContent;
        
        // Check if content looks like markdown
        if (rawContent.includes('#') || rawContent.includes('**') || rawContent.includes('*')) {
            content.innerHTML = marked.parse(rawContent);
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>