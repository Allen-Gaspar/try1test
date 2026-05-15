<?php
$host = "localhost";
$user = "root";
$pass = ""; // change if needed
$dbname = "sis_database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admins (e.g., username = 'admin')
$admin_sql = "SELECT * FROM tbluser WHERE username = 'admin'";
$admin_result = $conn->query($admin_sql);

// Get users (everyone except 'admin')
$user_sql = "SELECT * FROM tbluser WHERE username != 'admin'";
$user_result = $conn->query($user_sql);
?>

<style>
    .table tbody tr {
        height: 50px;
    }

    .table tbody td {
        padding: 10px 10px;
        vertical-align: middle;
    }

    .table thead th {
        background-color: #002B5B;
        color: white;
    }
</style>

<!-- Admins Card -->
<div class="card p-5 shadow mb-4">
    <div class="row">
        <div class="col-md-6 mb-4 mt-2">
            <h3 class="page-title mb-4"><i class="bi bi-person-badge-fill text-primary"></i> Admin </h3>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($admin_result->num_rows > 0): ?>
                <?php while($row = $admin_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No admins found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Users Card -->
<div class="card p-5 shadow mb-4">
    <div class="row">
        <div class="col-md-6 mb-4 mt-2">
            <h3 class="page-title mb-4"><i class="bi bi-people-fill text-primary"></i> Users</h3>
        </div>
        <div class="col-md-6 mb-4 mt-3 text-end">
            <p class="me-3"><b>Total Users: <?= $user_result->num_rows ?></b></p>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($user_result->num_rows > 0): ?>
                <?php while($row = $user_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<?php
$conn->close();
?>
