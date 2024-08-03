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

// Fetch user balance and username
$sql = "SELECT balance, username FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
$balance = $user['balance'];
$username = $user['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Market Management System</title>
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
        .balance {
            font-size: 16px;
            color: #6c757d;
        }
        .quick-links li {
            padding: 5px 0;
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
                    <li class="nav-item active">
                        <a class="nav-link" href="http://localhost/stock_market/dashboard.php#">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/stock_market/buy_stocks.php">Buy Stocks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/stock_market/sell_stocks.php">Sell Stocks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/stock_market/portfolio.php">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/stock_market/transaction_history.php">Transaction History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/stock_market/stock_returns_calculator.php">Stock Returns Calculator</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    Welcome, <?php echo htmlspecialchars($username); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Dashboard</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Available Balance: ₹<?php echo number_format($balance, 2); ?></h6>
                        <!-- Dashboard content goes here -->
                        <p class="card-text"> Developed by  : 
Guruprasad pujari,
Nandish kubsad</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">About this project </h5>
                    </div>
                    <div class="card-body">
                        <!-- Portfolio content goes here -->
                        <p class="card-text">An advanced web application using PHP and MySQL for real-time stock trading, portfolio management, and financial analysis. Features include user registration, stock purchasing and selling, portfolio tracking, and a dynamic stock returns calculator, aimed at enhancing financial literacy and investment decision-making.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group quick-links">
                            <li class="list-group-item"><a href="https://www.instagram.com/guruprasad__pj?igsh=MXFnYnZ6YzN6Nml6Yw==">guruprasad pujari</a></li>
                            <li class="list-group-item"><a href="https://www.linkedin.com/authwall?trk=bf&trkInfo=AQFZjXbo3qxyEAAAAZCdBVX47zw6oG877N3hQ1dUllcbb-Pht_8i_IcUVwNi15jGQg0K1Phc5jzISAjLL5P23UYfGFYDQ_e4KYkU8_JiV5EkujGR_hZCnj4wbYZ4Ln735FreS7E=&original_referer=&sessionRedirect=https%3A%2F%2Fwww.linkedin.com%2Fin%2Fguruprasad-pujari-2a4b44306%3Futm_source%3Dshare%26utm_campaign%3Dshare_via%26utm_content%3Dprofile%26utm_medium%3Dandroid_app">linked in </a></li>
                            <li class="list-group-item"><a href="https://github.com/guruprasad908">github</a></li>
                            <li class="list-group-item"><a href="http://localhost/stock_market/logout.php">logout </a></li>
                            <li class="list-group-item"><a href=""></a></li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Add Money</h5>
                    </div>
                    <div class="card-body">
                        <form action="add_money_process.php" method="post">
                            <div class="form-group">
                                <label for="amount">Amount (₹):</label>
                                <input type="number" class="form-control" id="amount" name="amount" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Money</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
