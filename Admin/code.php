<?php
session_start();
$conn = new mysqli("localhost", "root", "", "sis_database");

$result = $conn->query("SELECT * FROM cesslen_codess ORDER BY id DESC");
?>

<body class="p-5 bg-light">
<div class="card p-5 shadow mb-4">
    <h3><i class="bi bi-gem text-warning ms-1"></i> VIP Code</h3>

    <table class="table table-bordered bg-white">
        <thead class="table-light">
            <tr>
                <th>Code</th>
                <th>Created At</th>
                <th>Amount (₱)</th>
                <th>Status</th>
                <th>Redeemed By</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['code']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><?= htmlspecialchars($row['gift_amount']) ?></td>
                    <td><?= $row['redeemed_by'] ? 'Redeemed' : 'Redeemed' ?></td>
                    <td><?= htmlspecialchars($row['redeemed_by'] ?? 'Len') ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>