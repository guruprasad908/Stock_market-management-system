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

            // Check if user has enough stocks to sell
            $sql_check_user_stocks = "SELECT * FROM user_stocks WHERE user_id = $user_id AND stock_id = $stock_id";
            $result_check_user_stocks = $conn->query($sql_check_user_stocks);

            if ($result_check_user_stocks->num_rows > 0) {
                $user_stock = $result_check_user_stocks->fetch_assoc();
                $available_quantity = $user_stock['quantity'];

                if ($quantity <= $available_quantity) {
                    // Calculate sale amount
                    $sale_amount = $current_price * $quantity;

                    // Update user balance
                    $sql_update_balance = "UPDATE users SET balance = balance + $sale_amount WHERE id = $user_id";
                    $conn->query($sql_update_balance);

                    // Update user stocks
                    $new_quantity = $available_quantity - $quantity;
                    if ($new_quantity > 0) {
                        $sql_update_user_stocks = "UPDATE user_stocks SET quantity = $new_quantity WHERE user_id = $user_id AND stock_id = $stock_id";
                    } else {
                        $sql_update_user_stocks = "DELETE FROM user_stocks WHERE user_id = $user_id AND stock_id = $stock_id";
                    }
                    $conn->query($sql_update_user_stocks);

                    // Record transaction
                    $sql_record_transaction = "INSERT INTO transactions (user_id, stock_id, type, quantity, price_per_unit, amount) 
                                              VALUES ($user_id, $stock_id, 'sell', $quantity, $current_price, $sale_amount)";
                    $conn->query($sql_record_transaction);

                    // Redirect to portfolio or another page
                    header("Location: portfolio.php");
                    exit();
                } else {
                    echo "Error: Insufficient stocks to sell.";
                }
            } else {
                echo "Error: User does not own this stock.";
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
