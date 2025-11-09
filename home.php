<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include 'db.php';

$username = $_SESSION['username'];
$displayName = ucwords(str_replace(['_', '-'], ' ', $username));

// --- Filter Handling (FIXED) ---
$genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$language = isset($_GET['language']) ? trim($_GET['language']) : '';

// Build base query and dynamic WHERE clauses (use LIKE for partial/case-insensitive match)
$sql = "SELECT id, title, cover_image FROM books";
$where = [];
$params = [];
$types = '';

if ($genre !== '') {
    $where[] = "LOWER(genre) LIKE ?";
    $params[] = '%' . strtolower($genre) . '%';
    $types .= 's';
}
if ($category !== '') {
    $where[] = "LOWER(category) LIKE ?";
    $params[] = '%' . strtolower($category) . '%';
    $types .= 's';
}
if ($language !== '') {
    $where[] = "LOWER(language) LIKE ?";
    $params[] = '%' . strtolower($language) . '%';
    $types .= 's';
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY title ASC";

// Prepare and execute safely
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Prepare failed: " . mysqli_error($conn));
}
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Reader Home</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #1e1e2f;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 100%;
      margin: 0 auto;
      padding: 5px;
      background-color: white;
    }

    .home-header {
      width: 94%;
      background: linear-gradient(to right, #ff416c, #ff4b2b);
      color: white;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      border-radius: 10px 10px 0 0;
    }

    .home-header .nav-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .home-header .nav-links {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .home-header .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.3s ease;
    }

    .home-header .nav-links a:hover {
      color: #1e1e2f;
    }

    .welcome {
      margin: 30px 0;
      font-size: 22px;
      text-align: center;
    }

    .main-content {
      text-align: center;
    }

    .main-content h2 {
      margin-bottom: 10px;
      font-size: 24px;
    }

    .main-content p {
      color: #ccc;
      font-size: 16px;
    }

    .books-grid {
      margin-top: 40px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 20px;
      padding-bottom: 40px;
    }

    .book-card {
      background: #2e2e3f;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
     .book-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.5);
    }

    .book-card img {
      max-width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 8px;
    }

    .book-card h3 {
      margin-top: 10px;
      font-size: 16px;
      color: #fff;
    }

    /* Filter toggle and form */
    #filterToggle {
      background: none;
      border: none;
      cursor: pointer;
      float: right;
      margin-top: 20px;
    }

    #filterToggle img {
      width: 30px;
      height: 30px;
    }

    #filterForm {
      display: none;
      margin-top: 20px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      max-width: 100%;
    }

    #filterForm form {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
    }

    #filterForm select,
    #filterForm button {
      padding: 8px 12px;
      border-radius: 5px;
      border: none;
      font-size: 14px;
    }

    #filterForm button {
      background-color: #ff4b2b;
      color: white;
      cursor: pointer;
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

    @media (max-width: 600px) {
      .home-header {
        flex-direction: column;
        text-align: center;
      }

      .home-header .nav-links {
        justify-content: center;
      }

      .main-content h2 {
        font-size: 20px;
      }

      .welcome {
        font-size: 18px;
      }

      .book-card h3 {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="home-header">
      <div class="nav-title">
        📚 <strong>Book Logger</strong>
      </div>
      <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="books.php">Books</a>
        <a href="reviews.php">Review</a>
        <a href="add-book.php">Add Book</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>

    <!-- Welcome -->
    <div class="welcome">
      Welcome, <strong><?php echo htmlspecialchars($displayName); ?></strong> 👋
    </div>

    <!-- Main Text -->
    <div class="main-content">
      <h2>Your Reading Journey Starts Here</h2>
      <p>Track the books you've read, share reviews, and build your own virtual bookshelf.</p>

      <!-- Filter Toggle -->
      <button id="filterToggle">
        <img src="images/filter-icon.png" alt="Filter">
      </button>

      <!-- Filter Form -->
<div id="filterForm">
  <form method="GET" action="home.php" style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
    
    <!-- Genre Filter -->
    <select name="genre" style="padding: 10px; border-radius: 5px; background: #222; color: #fff;">
      <option value="">Genre</option>
      <option value="Thriller">Thriller</option>
      <option value="Historical">Historical</option>
      <option value="Fantasy">Fantasy</option>
      <option value="Drama">Drama</option>
      <option value="Romance">Romance</option>
      <option value="Philosophy">Philosophy</option>
      <option value="Horror">Horror</option>
      <option value="Mythology">Mythology</option>
    </select>

    <!-- Category Filter -->
    <select name="category" style="padding: 10px; border-radius: 5px; background: #222; color: #fff;">
      <option value="">Category</option>
      <option value="Fiction">Fiction</option>
      <option value="Non-fiction">Non-fiction</option>
      <option value="Classic">Classic</option>
      <option value="Contemporary">Contemporary</option>
    </select>

    <!-- Language Filter -->
    <select name="language" style="padding: 10px; border-radius: 5px; background: #222; color: #fff;">
      <option value="">Language</option>
      <option value="English">English</option>
      <option value="Tamil">Tamil</option>
      <option value="Hindi">Hindi</option>
      <option value="Kannada">Kannada</option>  
      <option value="Malayalam">Malayalam</option>
      <option value="Telugu">Telugu</option>
    </select>

    <button type="submit" style="padding: 10px 20px; border: none; border-radius: 5px; background: #ff6600; color: #fff; cursor: pointer;">
      Filter
    </button>

  </form>
</div>


      <!-- Books Grid -->
      <div class="books-grid">
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="book-card" onclick="window.location.href=\'books.php?id=' . $row['id'] . '\'">';
            if (!empty($row['cover_image']) && file_exists($row['cover_image'])) {
              echo '<img src="' . htmlspecialchars($row['cover_image']) . '" alt="Book Cover">';
            } else {
              echo '<img src="default-cover.png" alt="No Cover">';
            }
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            echo '</div>';
          }
        } else {
          echo '<p>No books found.</p>';
        }
        ?>
      </div>
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
  </div>

  <script>
    document.getElementById("filterToggle").addEventListener("click", function() {
      var filterForm = document.getElementById("filterForm");
      filterForm.style.display = filterForm.style.display === "none" ? "block" : "none";
    });
  </script>
</body>
</html>
