<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php'; // Adjust the path if 'db.php' is in a different directory

// Redirect to index.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate the input
    $amount = floatval($_POST['amount'] ?? 0);
    
    if ($amount > 0) {
        // Fetch the current balance of the user
        $sql = "SELECT balance FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $current_balance = $user['balance'];
        $stmt->close();

        // Calculate the new balance
        $new_balance = $current_balance + $amount;

        // Update the user's balance
        $sql = "UPDATE users SET balance = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $new_balance, $user_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to the add_money page with a success message
        $_SESSION['message'] = "Amount added successfully!";
        header("Location: add_money.php");
        exit();
    } else {
        // Redirect to the add_money page with an error message
        $_SESSION['error'] = "Invalid amount!";
        header("Location: add_money.php");
        exit();
    }
} else {
    // Redirect to the add_money page if the request method is not POST
    header("Location: add_money.php");
    exit();
}
?>
