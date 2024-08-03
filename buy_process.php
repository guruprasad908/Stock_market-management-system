<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['stock_id']) && isset($_POST['quantity'])) {
        $stock_id = $_POST['stock_id'];
        $quantity = intval($_POST['quantity']); // Ensure quantity is an integer

        // Fetch stock details
        $sql_stock = "SELECT * FROM stocks WHERE id = $stock_id";
        $result_stock = $conn->query($sql_stock);

        if ($result_stock->num_rows > 0) {
            $stock = $result_stock->fetch_assoc();
            $current_price = $stock['current_price'];

            // Calculate purchase amount
            $purchase_amount = $current_price * $quantity;

            // Check user balance
            $sql_user = "SELECT * FROM users WHERE id = $user_id";
            $result_user = $conn->query($sql_user);

            if ($result_user->num_rows > 0) {
                $user = $result_user->fetch_assoc();
                $user_balance = $user['balance'];

                if ($user_balance >= $purchase_amount) {
                    // Update user balance
                    $new_balance = $user_balance - $purchase_amount;
                    $sql_update_balance = "UPDATE users SET balance = $new_balance WHERE id = $user_id";
                    $conn->query($sql_update_balance);

                    // Insert into user_stocks
                    $sql_insert_user_stocks = "INSERT INTO user_stocks (user_id, stock_id, quantity, stock_symbol) 
                                               VALUES ($user_id, $stock_id, $quantity, '{$stock['symbol']}')";
                    $conn->query($sql_insert_user_stocks);

                    // Record transaction
                    $sql_record_transaction = "INSERT INTO transactions (user_id, stock_id, type, quantity, price_per_unit, amount) 
                                              VALUES ($user_id, $stock_id, 'buy', $quantity, $current_price, $purchase_amount)";
                    $conn->query($sql_record_transaction);

                    // Redirect to portfolio or another page
                    header("Location: portfolio.php");
                    exit();
                } else {
                    echo "Error: Insufficient balance.";
                }
            } else {
                echo "Error: User not found.";
            }
        } else {
            echo "Error: Stock not found.";
        }
    } else {
        echo "Error: Missing required data.";
    }
} else {
    echo "Error: Invalid request method.";
}
?>
