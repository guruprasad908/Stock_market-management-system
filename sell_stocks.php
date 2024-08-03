<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php';

// Redirect to index.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's balance
$sql_balance = "SELECT balance FROM users WHERE id = ?";
$stmt_balance = $conn->prepare($sql_balance);
$stmt_balance->bind_param("i", $user_id);
$stmt_balance->execute();
$result_balance = $stmt_balance->get_result();
$user = $result_balance->fetch_assoc();
$stmt_balance->close();

// Check if balance was fetched successfully
if ($user) {
    $balance = $user['balance'];
} else {
    $balance = 0.00; // Default to 0.00 if balance couldn't be fetched
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['stock_id']) && isset($_POST['quantity'])) {
        $stock_id = $_POST['stock_id'];
        $quantity = $_POST['quantity'];

        // Fetch stock price
        $sql_stock = "SELECT current_price FROM stocks WHERE id = ?";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("i", $stock_id);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        $stock = $result_stock->fetch_assoc();
        $stmt_stock->close();

        if ($stock) {
            $current_price = $stock['current_price'];
            $amount = $current_price * $quantity;

            // Check if user has enough stocks to sell
            $sql_user_stocks = "SELECT us.quantity, s.symbol, s.name FROM user_stocks us INNER JOIN stocks s ON us.stock_id = s.id WHERE us.user_id = ? AND us.stock_id = ?";
            $stmt_user_stocks = $conn->prepare($sql_user_stocks);
            $stmt_user_stocks->bind_param("ii", $user_id, $stock_id);
            $stmt_user_stocks->execute();
            $result_user_stocks = $stmt_user_stocks->get_result();
            $user_stock = $result_user_stocks->fetch_assoc();
            $stmt_user_stocks->close();

            if ($user_stock && $user_stock['quantity'] >= $quantity) {
                // Update user stocks
                $new_quantity = $user_stock['quantity'] - $quantity;
                if ($new_quantity > 0) {
                    $sql_update_user_stocks = "UPDATE user_stocks SET quantity = ? WHERE user_id = ? AND stock_id = ?";
                    $stmt_update_user_stocks = $conn->prepare($sql_update_user_stocks);
                    $stmt_update_user_stocks->bind_param("iii", $new_quantity, $user_id, $stock_id);
                    $stmt_update_user_stocks->execute();
                    $stmt_update_user_stocks->close();
                } else {
                    $sql_delete_user_stocks = "DELETE FROM user_stocks WHERE user_id = ? AND stock_id = ?";
                    $stmt_delete_user_stocks = $conn->prepare($sql_delete_user_stocks);
                    $stmt_delete_user_stocks->bind_param("ii", $user_id, $stock_id);
                    $stmt_delete_user_stocks->execute();
                    $stmt_delete_user_stocks->close();
                }

                // Update user balance
                $sql_update_balance = "UPDATE users SET balance = balance + ? WHERE id = ?";
                $stmt_update_balance = $conn->prepare($sql_update_balance);
                $stmt_update_balance->bind_param("di", $amount, $user_id);
                $stmt_update_balance->execute();
                $stmt_update_balance->close();

                // Insert into transactions
                $sql_transaction = "INSERT INTO transactions (user_id, stock_id, type, quantity, price_per_unit, amount, date, transaction_date) VALUES (?, ?, 'sell', ?, ?, ?, NOW(), NOW())";
                $stmt_transaction = $conn->prepare($sql_transaction);
                $stmt_transaction->bind_param("iiidd", $user_id, $stock_id, $quantity, $current_price, $amount);
                $stmt_transaction->execute();
                $stmt_transaction->close();

                echo "Stocks sold successfully!";
            } else {
                echo "Insufficient stocks to sell.";
            }
        } else {
            echo "Invalid stock selected.";
        }
    } else {
        echo "Stock ID and Quantity are required.";
    }
}

// Fetch user's stocks to display in the dropdown
$sql_user_stocks_dropdown = "SELECT us.stock_id, s.symbol, s.name FROM user_stocks us INNER JOIN stocks s ON us.stock_id = s.id WHERE us.user_id = ?";
$stmt_user_stocks_dropdown = $conn->prepare($sql_user_stocks_dropdown);
$stmt_user_stocks_dropdown->bind_param("i", $user_id);
$stmt_user_stocks_dropdown->execute();
$result_user_stocks_dropdown = $stmt_user_stocks_dropdown->get_result();
$stmt_user_stocks_dropdown->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Stocks - Stock Market Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand {
            color: #fff;
        }
        .navbar-text {
            color: #fff;
        }
        .content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Stock Market System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_money.php">Add Money</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sell_stocks.php">Sell Stocks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaction_history.php">Transaction History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    Balance: ₹<?php echo number_format($balance, 2); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Sell Stocks</h2>
        <div class="card">
            <div class="card-header">
                Sell Your Stocks
            </div>
            <div class="card-body">
                <form action="sell_stocks.php" method="post">
                    <div class="form-group">
                        <label for="stock_id">Select Stock:</label>
                        <select class="form-control" id="stock_id" name="stock_id" required>
                            <?php
                            while ($row = $result_user_stocks_dropdown->fetch_assoc()) {
                                echo '<option value="' . $row['stock_id'] . '">' . $row['symbol'] . ' - ' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Sell Stocks</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
