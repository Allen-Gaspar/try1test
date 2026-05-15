<?php
include 'auth.php'; // Must define $conn and session

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
	
$username = $_SESSION['username'];

// Handle POST request to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $contact  = $_POST['contact'];
    $email    = $_POST['email'];
    $address  = $_POST['address'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE tbluser SET fullname=?, contact=?, email=?, address=?, password=? WHERE username=?");
    $stmt->bind_param("sssssss", $fullname, $contact, $email, $address, $password, $username);
}

// Fetch user data to show in form
$stmt = $conn->prepare("SELECT * FROM tbluser WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="card p-5 shadow mb-4">
    <div class="row">
        <div class="col-md-6 mb-4 mt-2">
            <h3 class="page-title mb-3"><i class="bi bi-person-fill text-info"></i> Admin Profile</h3>
        </div>
        <div class="col-md-6 mt-3 text-end">
            <a href="#" onclick="enableEdit()" class="text-decoration-none" style="font-size: 1.3rem;">
    <i class="bi bi-pencil-square"></i>Edit
</a>



        </div>
    </div>

    <form method="POST">
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label"><b>Full Name</b></label>
                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" readonly>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label"><b>Contact</b></label>
                <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user['contact']) ?>" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label"><b>Email</b></label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label"><b>Address</b></label>
                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>" readonly>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label"><b>Username</b></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly disabled>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label"><b>Password</b></label>
                <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($user['password']) ?>" readonly>
            </div>
        </div>

        <div class="text-center mt-3 mb-2">
            <button type="submit" class="btn btn-primary px-4" id="saveBtn" disabled>Save Changes</button>
        </div>
    </form>
	
	
</div>


