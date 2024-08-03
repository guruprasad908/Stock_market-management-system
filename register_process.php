<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php'; // Adjust the path as per your file structure

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input (you can add more validation as needed)
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_hashed = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security

    // Example of more complex validation
    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit(); // Exit early if required fields are empty
    }

    // Check if the username already exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Username already exists. Please choose a different username.";
        exit();
    }

    // Insert new user into database
    $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ss", $username, $password_hashed);
    
    if ($insert_stmt->execute()) {
        // Registration successful
        echo "Registration successful. You can now <a href='login.php'>login</a>.";
    } else {
        echo "Registration failed. Please try again later.";
    }

    // Close prepared statements
    $check_stmt->close();
    $insert_stmt->close();
}

// Close database connection
$conn->close();
?>
