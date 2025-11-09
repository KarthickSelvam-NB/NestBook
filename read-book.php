<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';

$username = $_SESSION['username'];
$displayName = ucwords(str_replace(['_', '-'], ' ', $username));

$book_id = intval($_GET['id'] ?? 0);

// Fetch book details
$book = null;
if ($book_id) {
    $sql = "SELECT * FROM books WHERE id = $book_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result)) {
        $book = mysqli_fetch_assoc($result);
    }
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['review'])) {
    $rating = intval($_POST['rating']);
    $review = mysqli_real_escape_string($conn, $_POST['review']);

    $insert = "INSERT INTO reviews (book_id, username, rating, review) 
               VALUES ($book_id, '$username', $rating, '$review')";
    mysqli_query($conn, $insert);
}

// Fetch reviews
$reviews = [];
$rev_sql = "SELECT * FROM reviews WHERE book_id = $book_id ORDER BY created_at DESC";
$rev_result = mysqli_query($conn, $rev_sql);
if ($rev_result) {
    while ($row = mysqli_fetch_assoc($rev_result)) {
        $reviews[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Read Book - <?= $book ? htmlspecialchars($book['title']) : "Not Found"; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: Arial; background: #1e1e2f; margin: 0; color: #333; }
    .home-header { position: sticky; top: 0; z-index: 1000; background: linear-gradient(to right, #ff416c, #ff4b2b); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 10px 10px; }
    .home-header .nav-links a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .home-header .nav-links a:hover { color: #1e1e2f; }
    .container { max-width: 900px; background: white; margin: 80px auto 40px; padding: 30px 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h1 { color: #f25c54; }
    .book-detail p strong { color: #ff4b2b; }
    .download-link, .back-btn { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #f25c54; color: white; border-radius: 5px; text-decoration: none; }
    .download-link:hover, .back-btn:hover { background: #d8433b; }
    .review-form textarea, .review-form select { width: 100%; padding: 10px; margin-top: 10px; }
    .review-form button { margin-top: 10px; background: #ff4b2b; border: none; color: white; padding: 10px 15px; border-radius: 5px; font-weight: bold; cursor: pointer; }
    .review-form button:hover { background: #cc3a26; }
    .review-list { margin-top: 30px; }
    .review-list h3 { color: #f25c54; }
    .review-box { border-top: 1px solid #ccc; padding: 10px 0; }
    .not-found { text-align: center; font-weight: bold; color: red; }
  </style>
</head>
<body>

<div class="home-header">
  <div class="nav-title"><strong>📚 Book Logger</strong></div>
  <div class="nav-links">
    <a href="home.php">Home</a>
    <a href="books.php">Books</a>
    <a href="reviews.php">Review</a>
    <a href="add-book.php">Add Book</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <div class="welcome">Welcome, <strong><?= htmlspecialchars($displayName); ?></strong> 👋</div>

  <?php if ($book): ?>
    <h1><?= htmlspecialchars($book['title']); ?></h1>
    <div class="book-detail">
      <p><strong>Author:</strong> <?= htmlspecialchars($book['author']); ?></p>
      <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre']); ?></p>
      <p><strong>Category:</strong> <?= htmlspecialchars($book['category']); ?></p>
      <p><strong>Language:</strong> <?= htmlspecialchars($book['language']); ?></p>
      <p><strong>Pages:</strong> <?= $book['pages'] ?></p>
      <p><strong>Parts:</strong> <?= $book['parts'] ?></p>
      <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($book['full_description'])); ?></p>
    </div>

    <?php
      $path = $book['file_path'];
      $fullPath = __DIR__ . '/' . $path;
      if (!empty($path) && file_exists($fullPath)):
    ?>
      <a class="download-link" href="<?= htmlspecialchars($path); ?>" download>📥 Download Book</a>
    <?php else: ?>
      <p style="color: red; margin-top: 15px;">❌ Book PDF not available for download.</p>
    <?php endif; ?>

    <h2>Leave a Review</h2>
    <form class="review-form" method="post">
      <label>Rating:</label>
      <select name="rating" required>
        <option value="">Select</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
        <?php endfor; ?>
      </select>
      <label>Review:</label>
      <textarea name="review" rows="4" placeholder="Share your thoughts..." required></textarea>
      <button type="submit">Submit Review</button>
    </form>

    <div class="review-list">
      <h3>Reviews:</h3>
      <?php if ($reviews): ?>
        <?php foreach ($reviews as $rev): ?>
          <div class="review-box">
            <strong><?= htmlspecialchars($rev['username']); ?></strong> (<?= $rev['rating'] ?>⭐)
            <p><?= nl2br(htmlspecialchars($rev['review'])); ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No reviews yet. Be the first!</p>
      <?php endif; ?>
    </div>

    <a href="books.php" class="back-btn">← Back to Books</a>
  <?php else: ?>
    <div class="not-found">Book not found. Please check the link or return to the book list.</div>
    <a href="books.php" class="back-btn">← Back to Books</a>
  <?php endif; ?>
</div>

</body>
</html>
