<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$displayName = ucwords(str_replace(['_', '-'], ' ', $username));

include 'db.php';

// Fetch books
$sql = "SELECT * FROM books ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Books | NESTBOOK</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fff;
      color: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    /* Fixed Header */
    .home-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background: linear-gradient(to right, #ff416c, #ff4b2b);
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 999;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .nav-title {
      font-size: 22px;
      font-weight: bold;
    }

    .nav-links a {
      color: #fff;
      margin: 0 12px;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: #1e1e2f;
    }

    /* Main container */
    .content {
      flex: 1;
      padding: 120px 20px 60px 20px;
      width: 100%;
      max-width: 1400px;
      margin: 0 auto;
    }

    .welcome {
      font-size: 24px;
      margin-bottom: 25px;
      text-align: center;
      color: #1e1e2f;
    }

    h2 {
      text-align: center;
      color: #ff4b2b;
      margin-bottom: 20px;
    }

    .book-table {
      width: 100%;
      border-collapse: collapse;
      background: #2a2a3c;
      border-radius: 10px;
      overflow: hidden;
    }

    .book-table th, .book-table td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid #444;
      font-size: 16px;
    }

    .book-table th {
      background: #ff4b2b;
      color: #fff;
    }

    .book-table tr:hover {
      background-color: #3a3a4f;
      transition: 0.3s;
    }

    .read-btn {
      background: #f25c54;
      border: none;
      color: white;
      padding: 8px 14px;
      border-radius: 6px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
      display: inline-block;
    }

    .read-btn:hover {
      background-color: #d8433b;
    }

    /* Footer */
    footer {
      width: 100%;
      background: linear-gradient(to right, #ff416c, #ff4b2b);
      color: white;
      text-align: center;
      padding: 20px 10px;
      margin-top: auto;
      font-size: 14px;
    }

    footer a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
      font-weight: bold;
    }

    footer a:hover {
      text-decoration: underline;
    }

    footer p {
      margin: 8px 0 4px;
      font-size: 13px;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .book-table th, .book-table td {
        font-size: 14px;
        padding: 10px 8px;
      }

      .nav-links {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        text-align: center;
      }

      .nav-links a {
        margin: 5px;
      }

      .nav-title {
        font-size: 18px;
      }
    }

    @media (max-width: 600px) {
      .book-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }

      .content {
        padding: 100px 10px 60px 10px;
      }

      .welcome {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="home-header">
    <div class="nav-title">📚 NESTBOOK</div>
    <div class="nav-links">
      <a href="home.php">Home</a>
      <a href="books.php">Books</a>
      <a href="reviews.php">Review</a>
      <a href="add-book.php">Add Book</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="welcome">
      Welcome, <strong><?php echo htmlspecialchars($displayName); ?></strong> 👋
    </div>

    <h2>All Books</h2>
    <table class="book-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Genre</th>
          <th>Category</th>
          <th>Language</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['author']); ?></td>
              <td><?php echo htmlspecialchars($row['genre']); ?></td>
              <td><?php echo htmlspecialchars($row['category']); ?></td>
              <td><?php echo htmlspecialchars($row['language']); ?></td>
              <td>
                <a class="read-btn" href="read-book.php?id=<?php echo $row['id']; ?>">📖 Read</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align:center;">No books found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Footer -->
  <footer>
    &copy; <?php echo date('Y'); ?> NESTBOOK. All rights reserved.<br>
    <p>Designed & Developed by KarthickSelvam</p>
    <a href="home.php">Home</a> |
    <a href="books.php">Books</a> |
    <a href="reviews.php">Review</a> |
    <a href="add-book.php">Add Book</a> |
    <a href="logout.php">Logout</a>
  </footer>
</body>
</html>
