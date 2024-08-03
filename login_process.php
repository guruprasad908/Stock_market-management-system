<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php'; // Adjust the path as per your file structure

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if the username and password are provided
    if (!empty($username) && !empty($password)) {
        // Prepare SQL statement to fetch user from database
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // User found, verify password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct, start session and store user ID
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php"); // Redirect to dashboard or any other page
                exit();
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found.";
        }

        // Close prepared statement
        $stmt->close();
    } else {
        echo "Username and password are required.";
    }
}

// Close database connection
$conn->close();
?>
