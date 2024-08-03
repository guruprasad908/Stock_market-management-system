<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php'; // Adjust the path as per your file structure

// Redirect to index.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query to fetch available stocks (not already owned by the user)
$sql = "SELECT * FROM stocks WHERE symbol NOT IN 
        (SELECT stock_symbol FROM user_stocks WHERE user_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Stocks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
        }
        input[type="number"], select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: calc(100% - 22px);
        }
        button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buy Stocks</h2>
        <form action="buy_process.php" method="post">
            <label for="stock">Select Stock to Buy:</label>
            <select name="stock_id" id="stock" required>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?= $row['id']; ?>">
                        <?= $row['name']; ?> - ₹<?= $row['current_price']; ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" required><br><br>
            <button type="submit">Buy Stock</button>
        </form>
    </div>
</body>
</html>

<?php
// Close prepared statement and database connection
$stmt->close();
$conn->close();
?>
