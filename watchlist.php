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

// Query to fetch stocks in the user's watchlist
$sql = "SELECT stocks.symbol, stocks.name, stocks.current_price
        FROM stocks
        INNER JOIN watchlist ON stocks.symbol = watchlist.stock_symbol
        WHERE watchlist.user_id = '$user_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Watchlist</title>
</head>
<body>
    <h2>My Watchlist</h2>
    <table border="1">
        <tr>
            <th>Symbol</th>
            <th>Name</th>
            <th>Current Price</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['symbol']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>₹{$row['current_price']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
