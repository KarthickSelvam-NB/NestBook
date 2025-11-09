<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

$username = $_SESSION['username'];

// Fetch books with average rating and review count
$sql = "SELECT b.id, b.title, b.author, b.genre, b.category, b.language, b.pages, b.parts, b.full_description, b.file_path,
               IFNULL(AVG(r.rating), 0) AS avg_rating,
               COUNT(r.id) AS review_count
        FROM books b
        LEFT JOIN reviews r ON b.id = r.book_id
        GROUP BY b.id
        ORDER BY avg_rating DESC, review_count DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Book Reviews</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  body {
    font-family: Arial, sans-serif;
    background: white;
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
    max-width: 100%;
    margin: 30px auto;
    background: linear-gradient(to right, #ff416c, #ff4b2b);
    padding: 30px;
    border-radius: 10px;
  }
  h1 {
    margin-top: 0;
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
  .rating {
    font-weight: bold;
    color: #ffd700; /* gold for stars */
  }
  .download-btn {
    display: inline-block;
    background: #1e1e2f;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 10px;
  }
  .download-btn:hover {
    background: #33334d;
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
    <a href="reviews.php">Reviews</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<div class="container">
  <h1>Books Sorted by Reviews</h1>

  <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($book = mysqli_fetch_assoc($result)): ?>
      <div class="book">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <div class="details">
          Author: <?= htmlspecialchars($book['author']) ?> | Genre: <?= htmlspecialchars($book['genre']) ?> | Language: <?= htmlspecialchars($book['language']) ?> | Pages: <?= (int)$book['pages'] ?>
        </div>
        <div class="details">
          Average Rating: <span class="rating"><?= number_format($book['avg_rating'], 2) ?></span> 
          (<?= (int)$book['review_count'] ?> review<?= $book['review_count'] != 1 ? 's' : '' ?>)
        </div>
        <p><?= nl2br(htmlspecialchars($book['full_description'])) ?></p>

        <?php if (!empty($book['file_path']) && file_exists($book['file_path'])): ?>
          <a class="download-btn" href="<?= htmlspecialchars($book['file_path']) ?>" download>Download PDF</a>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No books found.</p>
  <?php endif; ?>

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
