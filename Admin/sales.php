<?php
session_start();
include '../other-functions/db_connect.php';

// Top 10 active users
$userQuery = "SELECT username, SUM(quantity) AS total_quantity 
              FROM ordered_items 
              GROUP BY username 
              ORDER BY total_quantity DESC 
              LIMIT 10";
$userResult = $conn->query($userQuery);
$topUsers = [];
if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $topUsers[] = $row;
    }
}

// Top 10 best-selling products
$productQuery = "SELECT product_name, SUM(quantity) AS total_quantity 
                 FROM ordered_items 
                 GROUP BY product_name 
                 ORDER BY total_quantity DESC 
                 LIMIT 10";
$productResult = $conn->query($productQuery);
$topProducts = [];
if ($productResult) {
    while ($row = $productResult->fetch_assoc()) {
        $topProducts[] = $row;
    }
}

// Function to return rank icon (medal) only for top 3
function getRankIcon($rank) {
    switch ($rank) {
        case 0: return "🥇";
        case 1: return "🥈";
        case 2: return "🥉";
        default: return "";
    }
}
?>

<body class="p-5 bg-light">
<div class="card p-5 shadow mb-4">
    <h3 class="text-center mb-4"><i class="bi bi-bar-chart-fill text-danger"></i> Sales Dashboard - Summary Tables</h3>

    <div class="row">
        <!-- Top Users -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center bg-primary text-white">
                    Top 10 Active Users
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Username</th>
                                <th>Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($topUsers) > 0): ?>
                                <?php foreach ($topUsers as $index => $user): ?>
                                    <?php
                                        $isCurrentUser = isset($_SESSION['username']) && $_SESSION['username'] === $user['username'];
                                        $usernameDisplay = $isCurrentUser ? "You ({$user['username']})" : htmlspecialchars($user['username']);
                                        $rowClass = $isCurrentUser ? 'table-danger fw-bold' : '';
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <!-- Rank column: just the number -->
                                        <td><?= $index + 1 ?></td>
                                        <!-- Username column: medal icon + name -->
                                        <td class="text-start ps-3"><?= getRankIcon($index) . ' ' . $usernameDisplay ?></td>
                                        <td><?= (int)$user['total_quantity'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3">No data found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center bg-success text-white">
                    Top 10 Best-Selling Products
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Product Name</th>
                                <th>Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($topProducts) > 0): ?>
                                <?php foreach ($topProducts as $index => $product): ?>
                                    <tr>
                                        <!-- Rank column: just the number -->
                                        <td><?= $index + 1 ?></td>
                                        <!-- Product column: medal icon + product name -->
                                        <td class="text-start ps-3"><?= getRankIcon($index) . ' ' . htmlspecialchars($product['product_name']) ?></td>
                                        <td><?= (int)$product['total_quantity'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3">No data found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
