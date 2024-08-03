<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM stocks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Future Predictions</title>
</head>
<body>
    <h2>Future Predictions</h2>
    <table border="1">
        <tr>
            <th>Stock Symbol</th>
            <th>Name</th>
            <th>Current Price (₹)</th>
            <th>Future Prediction</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['symbol']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['current_price']}</td>
                    <td>{$row['future_prediction']}</td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
