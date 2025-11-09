
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pages` int(11) DEFAULT NULL,
  `parts` int(11) DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `books` (`id`, `title`, `author`, `genre`, `category`, `language`, `created_at`, `pages`, `parts`, `full_description`, `file_path`, `cover_image`, `image_path`) VALUES
(33, 'The Silent Patient', 'Alex Michaelides', 'Thriller', 'Fiction', 'English', '2025-11-09 08:08:52', 336, 1, 'A psychological thriller about a woman\'s act of violence against her husband—and the therapist obsessed with uncovering her motive.', 'uploads/the-silent-patient.pdf', 'uploads/covers/the-silent-patient.jpg', NULL),
(34, 'Ponniyin Selvan', 'Kalki Krishnamurthy', 'Historical', 'Fiction', 'Tamil', '2025-11-09 08:08:52', 2400, 5, 'A historical novel set in the Chola dynasty era, filled with intrigue, adventure, and political drama.', 'uploads/ponniyin-selvan.pdf', 'uploads/covers/ponniyin-selvan.jpg', NULL),
(35, 'Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Fantasy', 'Fiction', 'English', '2025-11-09 08:08:52', 309, 1, 'A young boy discovers he is a wizard and begins his magical education at Hogwarts School of Witchcraft and Wizardry.', 'uploads/harry-potter-1.pdf', 'uploads/covers/harry-potter-1.jpg', NULL),
(36, 'The Guide', 'R.K. Narayan', 'Drama', 'Classic', 'Hindi', '2025-11-09 08:08:52', 252, 1, 'A charming story of Raju, a railway guide who transforms into a spiritual guide and then a revered holy man.', 'uploads/the-guide.pdf', 'uploads/covers/the-guide.jpg', NULL),
(37, 'Love and Other Words', 'Christina Lauren', 'Romance', 'Contemporary', 'English', '2025-11-09 08:08:52', 432, 1, 'A second-chance romance between childhood sweethearts who reconnect after years of separation.', 'uploads/love-and-other-words.pdf', 'uploads/covers/love-and-other-words.jpg', NULL),
(38, 'Kadavul', 'Su. Thirunavukkarasar', 'Philosophy', 'Non-fiction', 'Tamil', '2025-11-09 08:08:52', 180, 1, 'A spiritual and philosophical exploration of God, faith, and the human journey through life.', 'uploads/kadavul.pdf', 'uploads/covers/kadavul.jpg', NULL),
(39, 'The Shining', 'Stephen King', 'Horror', 'Fiction', 'English', '2025-11-09 08:08:52', 447, 1, 'A family heads to an isolated hotel for the winter where a sinister presence influences the father into violence.', 'uploads/the-shining.pdf', 'uploads/covers/the-shining.jpg', NULL),
(40, 'Raavan: Enemy of Aryavarta', 'Amish Tripathi', 'Mythology', 'Fiction', 'Hindi', '2025-11-09 08:08:52', 374, 1, 'A fictionalized retelling of Raavan\'s life, portraying him as a brilliant but ruthless leader driven by ambition and vengeance.', 'uploads/raavan-enemy-of-aryavarta.pdf', 'uploads/covers/raavan-enemy-of-aryavarta.jpg', NULL);

-- --------------------------------------------------------


-- Table structure for table `reviews`

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `reviews`

INSERT INTO `reviews` (`id`, `book_id`, `username`, `rating`, `review`, `created_at`) VALUES
(1, 34, 'Karthick Selvam NB', 5, 'such a amazing novel..it helps to me for lots and it reduce my stress & depression ', '2025-11-09 08:14:39');

-- --------------------------------------------------------

-- Table structure for table `users`

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Karthick Selvam NB', NULL, 'karthick@gmail.com', '$2y$10$HfqXoAayfFrXgKogaoVdT.lAqsV1qHd95Gvzej3oYtHniz1zcIcie', '2025-11-09 07:55:41');


-- Indexes for dumped tables



-- Indexes for table `books`

ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

-- Indexes for table `reviews`
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;
