<?php
require_once 'config/database.php';
require_once 'includes/session.php';

requireLogin();

$blog_id = $_GET['id'] ?? 0;
$user_id = getCurrentUserId();

$conn = getDBConnection();

// Fetch blog post and verify ownership
$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$post = $result->fetch_assoc();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $error = 'Title and content are required';
    } else {
        $stmt = $conn->prepare("UPDATE blogPost SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $content, $blog_id, $user_id);
        
        if ($stmt->execute()) {
            header("Location: view-blog.php?id=" . $blog_id);
            exit();
        } else {
            $error = 'Failed to update blog post';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - Blog App</title>
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
        <h1 class="page-title">Edit Blog Post</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="blog-form">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
            
            <div class="form-group">
                <label>Content (Markdown supported)</label>
                <textarea id="markdown-editor" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="view-blog.php?id=<?php echo $blog_id; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
    <script>
        var simplemde = new SimpleMDE({ 
            element: document.getElementById("markdown-editor"),
            spellChecker: false,
            status: false
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>