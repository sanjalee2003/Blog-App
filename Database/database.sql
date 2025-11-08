-- Blog Application Database Schema
-- IN2120 - Web Programming Assignment

-- Create database
CREATE DATABASE IF NOT EXISTS blog_db;
USE blog_db;

-- Drop tables if they exist (for fresh install)
DROP TABLE IF EXISTS blogPost;
DROP TABLE IF EXISTS user;

-- Create user table
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create blogPost table
CREATE TABLE blogPost (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_user_username ON user(username);
CREATE INDEX idx_user_email ON user(email);
CREATE INDEX idx_blogpost_user_id ON blogPost(user_id);
CREATE INDEX idx_blogpost_created_at ON blogPost(created_at);

-- Insert sample users (passwords are all 'password123')
INSERT INTO user (username, email, password, role) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample blog posts
INSERT INTO blogPost (user_id, title, content) VALUES
(1, 'Welcome to Our Blog Platform', '# Welcome to Our Blog!\n\nThis is a sample blog post to demonstrate the features of our blog application.\n\n## Features\n\n- **Markdown Support**: Write your posts in Markdown\n- **User Authentication**: Secure login system\n- **CRUD Operations**: Create, Read, Update, Delete\n\n## Getting Started\n\nTo create your own blog post:\n\n1. Register an account\n2. Login\n3. Click the "Create Blog" button\n4. Start writing!\n\nHappy blogging!'),

(2, 'Introduction to Web Development', '# Web Development Basics\n\nWeb development is the work involved in developing a website for the Internet.\n\n## Key Technologies\n\n1. **HTML** - Structure\n2. **CSS** - Styling\n3. **JavaScript** - Interactivity\n4. **PHP** - Server-side logic\n5. **MySQL** - Database\n\n## Frontend vs Backend\n\n**Frontend** deals with what users see and interact with.\n**Backend** handles server logic and database operations.\n\n```javascript\n// Example JavaScript code\nfunction greet(name) {\n    return `Hello, ${name}!`;\n}\n```'),

(3, 'The Power of Markdown', '# Why Markdown?\n\nMarkdown is a lightweight markup language that you can use to add formatting elements to plaintext text documents.\n\n## Advantages\n\n- Easy to learn\n- Platform independent\n- Clean and readable\n- Widely supported\n\n## Common Syntax\n\n### Emphasis\n\n*italic* or _italic_\n**bold** or __bold__\n\n### Lists\n\n- Item 1\n- Item 2\n  - Nested item\n\n### Links\n\n[Google](https://www.google.com)\n\n### Code\n\nInline `code` or code blocks:\n\n```python\ndef hello_world():\n    print("Hello, World!")\n```\n\nMarkdown makes writing for the web easy!');

-- Display success message
SELECT 'Database setup completed successfully!' AS message;