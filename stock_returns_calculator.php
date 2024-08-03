<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
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

// Initialize variables
$return_percentage = null;

// Fetch user's stocks
$sql = "SELECT us.quantity, s.symbol, s.name, s.current_price FROM user_stocks us
        INNER JOIN stocks s ON us.stock_id = s.id
        WHERE us.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$user_stocks = [];
while ($row = $result->fetch_assoc()) {
    $user_stocks[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $stock_symbol = $_POST['stock_symbol'] ?? '';
    $purchase_price = floatval($_POST['purchase_price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);

    // Find the stock in user's portfolio
    $selected_stock = null;
    foreach ($user_stocks as $stock) {
        if ($stock['symbol'] == $stock_symbol) {
            $selected_stock = $stock;
            break;
        }
    }

    if ($selected_stock) {
        $current_price = $selected_stock['current_price'];
        $total_investment = $purchase_price * $quantity;
        $current_value = $current_price * $quantity;
        $return_percentage = (($current_value - $total_investment) / $total_investment) * 100;

        // Calculate profit amount
        $profit_amount = $current_value - $total_investment;
    } else {
        echo "Stock not found in your portfolio.";
    }
}

// Close prepared statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Returns Calculator</title>
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
        .result {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Stock Returns Calculator</h2>
        <form method="post" action="">
            <label for="stock_symbol">Stock Symbol:</label>
            <select name="stock_symbol" required>
                <?php foreach ($user_stocks as $stock): ?>
                    <option value="<?= $stock['symbol']; ?>"><?= $stock['name']; ?> - ₹<?= $stock['current_price']; ?></option>
                <?php endforeach; ?>
            </select><br>
            <label for="purchase_price">Purchase Price (₹):</label>
            <input type="number" step="0.01" name="purchase_price" required><br>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" required><br>
            <button type="submit">Calculate</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($return_percentage)): ?>
            <div class="result">
                <h3>Return Percentage: <?= number_format($return_percentage, 2); ?>%</h3>
                <h3>Profit Amount: ₹<?= number_format($profit_amount, 2); ?></h3>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
