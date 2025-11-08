<?php
require_once 'config/database.php';
require_once 'includes/session.php';

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = getCurrentUserId();
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        
        if ($stmt->execute()) {
            $blog_id = $stmt->insert_id;
            header("Location: view-blog.php?id=" . $blog_id);
            exit();
        } else {
            $error = 'Failed to create blog post';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog - Blog App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php">BlogApp</a>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="btn btn-secondary">Home</a>
                <span>Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Create New Blog Post</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="blog-form">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" placeholder="Enter blog title">
            </div>
            
            <div class="form-group">
                <label>Content (Markdown supported)</label>
                <textarea id="markdown-editor" name="content" ></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Publish</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <script>
        var simplemde = new SimpleMDE({ 
            element: document.getElementById("markdown-editor"),
            spellChecker: false,
            placeholder: "Write your blog content here...\n\nYou can use Markdown formatting:\n# Heading\n**bold** *italic*\n- List items",
            status: false
        });
    </script>
</body>
</html>