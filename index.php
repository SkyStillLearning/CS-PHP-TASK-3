<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $review = htmlspecialchars($_POST['review']);

    // Database connection
    $servername = "localhost";
    $username = "root"; // Update if different
    $password = ""; // Update if different
    $dbname = "reviews";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insert data
        $stmt = $conn->prepare("INSERT INTO user_reviews (name, email, review, submission_date) VALUES (:name, :email, :review, NOW())");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':review', $review);

        $stmt->execute();
        $success_message = "Review submitted successfully!";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }

    $conn = null; // Close connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submission</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; margin: auto; }
        input, textarea, button { width: 100%; margin-bottom: 16px; }
        .message { margin: 10px auto; max-width: 400px; text-align: center; }
    </style>
</head>
<body>
    <h2>Submit Your Review Here!</h2>

    <!-- Display success or error message -->
    <?php if (!empty($success_message)): ?>
        <div class="message" style="color: green;"><?php echo $success_message; ?></div>
    <?php elseif (!empty($error_message)): ?>
        <div class="message" style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Form -->
    <form action="index.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="review">What do you think about our product ?:</label>
        <textarea id="review" name="review" rows="4" required></textarea>
        
        <button type="submit">Submit</button>
    </form>
</body>
</html>


<?php
// Fetch all reviews
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT * FROM user_reviews ORDER BY submission_date DESC");
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- Display Reviews -->
<h2>All Reviews</h2>
<?php if (!empty($reviews)): ?>
    <div>
        <?php foreach ($reviews as $review): ?>
            <div>
                <strong><?php echo htmlspecialchars($review['name']); ?></strong> 
                (<em><?php echo htmlspecialchars($review['email']); ?></em>) said:
                <p><?php echo htmlspecialchars($review['review']); ?></p>
                <small>Submitted on <?php echo $review['submission_date']; ?></small>
                <hr>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No reviews yet. Be the first to submit !</p>
<?php endif; ?>
