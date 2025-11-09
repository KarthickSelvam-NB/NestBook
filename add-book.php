<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

$username = $_SESSION['username'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $language = trim($_POST['language'] ?? '');
    $pages = intval($_POST['pages'] ?? 0);
    $parts = intval($_POST['parts'] ?? 1);
    $full_description = trim($_POST['full_description'] ?? '');

    if (!$title || !$author || !$genre || !$category || !$language || !$pages) {
        $error = "Please fill all required fields.";
    } elseif (!isset($_FILES['book_pdf']) || $_FILES['book_pdf']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please upload a valid PDF file.";
    } else {
        $fileTmpPath = $_FILES['book_pdf']['tmp_name'];
        $fileName = $_FILES['book_pdf']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'pdf') {
            $error = "Only PDF files are allowed.";
        } else {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = uniqid('book_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $titleEsc = mysqli_real_escape_string($conn, $title);
                $authorEsc = mysqli_real_escape_string($conn, $author);
                $genreEsc = mysqli_real_escape_string($conn, $genre);
                $categoryEsc = mysqli_real_escape_string($conn, $category);
                $languageEsc = mysqli_real_escape_string($conn, $language);
                $descEsc = mysqli_real_escape_string($conn, $full_description);

                $insertSql = "INSERT INTO books (title, author, genre, category, language, pages, parts, full_description, file_path)
                              VALUES ('$titleEsc', '$authorEsc', '$genreEsc', '$categoryEsc', '$languageEsc', $pages, $parts, '$descEsc', '$destPath')";

                if (mysqli_query($conn, $insertSql)) {
                    $success = "Book added successfully!";
                } else {
                    $error = "Database error: " . mysqli_error($conn);
                    unlink($destPath);
                }
            } else {
                $error = "Error moving uploaded file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add Book</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  body {
    font-family: Arial, sans-serif;
    background: #1e1e2f;
    color: white;
    margin: 0;
    padding: 0;
  }
  header {
    background: #ff4b2b;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  header .nav-links a {
    color: white;
    margin-left: 20px;
    text-decoration: none;
    font-weight: bold;
  }
  header .nav-links a:hover {
    text-decoration: underline;
  }
  header .greeting {
    font-weight: bold;
  }
  .container {
    max-width: 600px;
    margin: 30px auto;
    background: linear-gradient(to right, #ff416c, #ff4b2b);
    padding: 30px;
    border-radius: 10px;
  }
  label {
    display: block;
    margin-top: 15px;
  }
  input[type=text], input[type=number], textarea, select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border-radius: 5px;
    border: none;
  }
  input[type=file] {
    margin-top: 10px;
  }
  button {
    margin-top: 20px;
    background: #1e1e2f;
    border: none;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
  }
  button:hover {
    background: #33334d;
  }
  .error {
    color: #ff6666;
  }
  .success {
    color: #66ff66;
  }
  p a {
    color: #1e1e2f;
    font-weight: bold;
    text-decoration: none;
  }
  p a:hover {
    text-decoration: underline;
  }
  footer {
      background: linear-gradient(to right, #ff416c, #ff4b2b);
      color: white;
      padding: 20px;
      text-align: center;
      border-radius: 0 0 10px 10px;
      font-size: 14px;
    }

    footer a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
      font-weight: bold;
    }

    footer a:hover {
      text-decoration: underline;
    }
</style>
</head>
<body>

<header>
  <div class="greeting">Hello, <?= htmlspecialchars($username) ?>!👋</div>
  <nav class="nav-links">
    <a href="home.php">Home</a>
    <a href="books.php">Books</a>
    <a href="add-book.php">Add Book</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h1>Add New Book</h1>
  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
  <?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" novalidate>
    <label>Title*</label>
    <input type="text" name="title" required />

    <label>Author*</label>
    <input type="text" name="author" required />

    <label>Genre*</label>
    <input type="text" name="genre" required />

    <label>Category*</label>
    <input type="text" name="category" required />

    <label>Language*</label>
    <input type="text" name="language" required />

    <label>Pages*</label>
    <input type="number" name="pages" min="1" required />

    <label>Parts</label>
    <input type="number" name="parts" min="1" value="1" />

    <label>Full Description</label>
    <textarea name="full_description" rows="5"></textarea>

    <label>Upload PDF*</label>
    <input type="file" name="book_pdf" accept="application/pdf" required />

    <button type="submit">Add Book</button>
  </form>

  <p><a href="books.php">← Back to Books</a></p>
</div>
<!-- Footer -->
  <footer>
    &copy; <?php echo date('Y'); ?> Book Logger. All rights reserved.<br>
    <p>Designed & Developed by KarthickSelvam</p>
    <a href="home.php">Home</a> |
    <a href="books.php">Books</a> |
    <a href="reviews.php">Review</a> |
    <a href="add-book.php">Add Book</a> |
    <a href="logout.php">Logout</a>
  </footer>
</body>
</html>
