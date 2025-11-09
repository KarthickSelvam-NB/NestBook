<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db.php'; // your database connection file

$username = $_SESSION['username'];
$displayName = ucwords(str_replace(['_', '-'], ' ', $username));

// Retrieve filters from GET parameters, default to empty string if not set
$genre = trim($_GET['genre'] ?? '');
$category = trim($_GET['category'] ?? '');
$language = trim($_GET['language'] ?? '');

// Build query dynamically depending on filters
$where = [];
$params = [];
$types = '';

if ($genre !== '') {
    $where[] = "LOWER(TRIM(genre)) = ?";
    $params[] = strtolower($genre);
    $types .= 's';
}

if ($category !== '') {
    $where[] = "LOWER(TRIM(category)) = ?";
    $params[] = strtolower($category);
    $types .= 's';
}

if ($language !== '') {
    $where[] = "LOWER(TRIM(language)) = ?";
    $params[] = strtolower($language);
    $types .= 's';
}

$sql = "SELECT * FROM books";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY title ASC";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("SQL Prepare failed: " . htmlspecialchars(mysqli_error($conn)));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// TEMPORARY DEBUG OUTPUT – remove after testing
echo "<pre>SQL: $sql\nParams: " . print_r($params, true) . "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Filtered Books</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #1e1e2f;
    color: white;
    margin: 0; padding: 0;
  }
  header {
    background: #ff4b2b;
    padding: 15px 30px;
    display: flex; justify-content: space-between; align-items: center;
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
    max-width: 900px;
    margin: 30px auto;
    background: linear-gradient(to right, #ff416c, #ff4b2b);
    padding: 30px;
    border-radius: 10px;
  }
  h1 {
    margin-top: 0;
    text-align: center;
  }
  .book {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 20px;
  }
  .book h2 {
    margin: 0 0 8px 0;
  }
  .book .details {
    font-size: 0.9em;
    margin-bottom: 10px;
  }
  .download-btn {
    display: inline-block;
    background: #1e1e2f;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
  }
  .download-btn:hover {
    background: #33334d;
  }
  .no-results {
    text-align: center;
    font-size: 18px;
    color: #ffcccc;
  }
  a.back-link {
    display: inline-block;
    margin-top: 15px;
    color: white;
    text-decoration: underline;
    font-weight: bold;
  }
</style>
</head>
<body>

<header>
  <div class="greeting">Hello, <?= htmlspecialchars($displayName) ?>!</div>
  <nav class="nav-links">
    <a href="home.php">Home</a>
    <a href="books.php">Books</a>
    <a href="reviews.php">Review</a>
    <a href="add-book.php">Add Book</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h1>Books Matching Your Filters</h1>

  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <?php while ($book = mysqli_fetch_assoc($result)): ?>
      <div class="book">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <div class="details">
          Author: <?= htmlspecialchars($book['author']) ?> |
          Genre: <?= htmlspecialchars($book['genre']) ?> |
          Category: <?= htmlspecialchars($book['category']) ?> |
          Language: <?= htmlspecialchars($book['language']) ?> |
          Pages: <?= (int)$book['pages'] ?>
        </div>
        <p><?= nl2br(htmlspecialchars($book['full_description'])) ?></p>

        <?php if (!empty($book['file_path']) && file_exists($book['file_path'])): ?>
          <a class="download-btn" href="<?= htmlspecialchars($book['file_path']) ?>" download>Download PDF</a>
        <?php else: ?>
          <p><em>PDF file not available</em></p>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="no-results">No books found matching your filters.</p>
  <?php endif; ?>

  <a href="home.php" class="back-link">← Back to Home</a>
</div>
</body>
</html>
