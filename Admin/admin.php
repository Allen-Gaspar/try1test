<?php
session_start();
include 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $username = $_SESSION['username'] ?? null;

    if ($username) {
        $update_stmt = $conn->prepare("UPDATE tbluser SET fullname = ?, contact = ?, email = ?, address = ?, password = ? WHERE username = ?");
        $update_stmt->bind_param("ssssss", $fullname, $contact, $email, $address, $password, $username);
        $update_stmt->execute();

        $_SESSION['toast_message'] = "Your information has been updated successfully!";
        $_SESSION['toast_type'] = "success";

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$toast_message = $_SESSION['toast_message'] ?? "";
$toast_type = $_SESSION['toast_type'] ?? "info";
unset($_SESSION['toast_message'], $_SESSION['toast_type']);
?>

<?php

$page = $_GET['page'] ?? ($_SESSION['last_page'] ?? 'profile');
$_SESSION['last_page'] = $page;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../other-functions/bootstrap.min.css" />
    <link rel="stylesheet" href="../bootstrap-icons-1.11.3/font/bootstrap-icons.css" />
	
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
        }

        .fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1050;
        }

        .top-banner {
            background-color: #E4ADFF;
            text-align: center;
            padding: 8px;
            font-weight: bold;
        }

        .navbar {
            background-color: #7388FE;
            padding: 13px 20px;
        }

        .navbar-nav .nav-link {
            color: white;
            font-size: 1.1rem;
            margin-right: 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            transform: scale(1.05);
            color: #e0d2f0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .navbar-nav .nav-link i {
            margin-right: 6px;
            font-size: 1.3rem;
        }

        #sidebar {
            width: 290px;
            background-color: rgb(28,40,65);
            color: white;
            position: fixed;
            top: 102px;
            left: -290px;
            bottom: 0;
            overflow-y: auto;
            transition: left 0.4s cubic-bezier(0.77, 0, 0.175, 1);
            padding: 20px;
            z-index: 999;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.2);
        }

        #sidebar.active {
            left: 0;
        }

        #sidebar .nav-link {
			display: flex;
			align-items: center;
			padding: 18px 30px;
			border-radius: 8px;
			transition: all 0.3s ease;
			font-size: 1rem; /* increased font size */
			gap: 5px; /* space between icon and text */
		}
		#sidebar .nav-link i {
			font-size: 1.2rem; /* increased icon size */
			flex-shrink: 0; /* prevents icon from shrinking */
		}
        #sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            font-size: 1.1rem;
        }

        #sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        .account-wrapper {
            max-width: 1000px;
            margin: 150px auto 100px auto;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .card-body i {
            font-size: 2rem;
            color: #7388FE;
        }

        #toggleSidebarBtn {
            border: none;
            background-color: white;
            color: #7388FE;
            font-size: 1.3rem;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        #toggleSidebarBtn:hover {
            background-color: #7388FE;
            color: white;
            box-shadow: 0 0 8px rgba(115, 136, 254, 0.6);
            transform: scale(1.05);
        }

        #toggleSidebarBtn.active {
            background-color: #7388FE;
            color: white;
            box-shadow: 0 0 10px rgba(115, 136, 254, 0.8);
            transform: rotate(90deg);
        }

        #sidebar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            transform: scale(1.08);
        }

        input[readonly], textarea[readonly] {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .non-editable {
            background-color: #e9ecef;
            border-color: #ced4da;
            cursor: not-allowed;
        }

        #pageWrapper {
            transition: margin-left 0.3s ease, width 0.3s ease;
            margin-left: 0;
            width: 100%;
        }

        #pageWrapper.sidebar-active {
            margin-left: 285px;
            width: calc(100% - 285px);
        }
    </style>
</head>
<body>
<div id="pageWrapper" class="sidebar-active">
    <div class="fixed-header">
        <div class="top-banner">
            <i class="bi bi-gem me-2" style="color: #06b5dd;"></i> Welcome, Admin!
        </div>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-light me-3" type="button" id="toggleSidebarBtn">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="fw-bold text-white fs-4"> <i class="bi bi-person-circle"></i> Developer Panel</span>
                </div>
				<ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../topnav/nav.php" title="Home">
                            <i class="bi bi-shop me-1 fs-5"></i> <span class="fs-5">Shop</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

<div id="sidebar" class="active">
<h4 class="text-white mt-3 px-4">ADMIN</h4>
        <ul class="nav flex-column mt-3" id="sidebar-nav">
            <li class="nav-item mb-1 mt-1">
                <a href="#" class="nav-link text-white active" data-page="profile.php"><i class="bi bi-person text-info ms-1"></i> Profile</a>
            </li>
            <li class="nav-item mb-1">
  <a href="#" class="nav-link text-white" data-page="users.php"><i class="bi bi-people text-primary ms-1"></i> Users</a>
</li>

            <li class="nav-item mb-1">
                <a href="#" class="nav-link text-white" data-page="sales.php"><i class="bi bi-graph-up-arrow text-danger ms-1"></i> Sales</a>
            </li>
			<li class="nav-item mb-1">
  <a href="#" class="nav-link text-white" data-page="products.php">
    <i class="bi bi-box-seam text-success ms-1"></i> Products
  </a>
</li>



            <li class="nav-item mb-1">
                <a href="#" class="nav-link text-white" data-page="code.php"><i class="bi bi-gem text-warning ms-1"></i> VIP Code</a>
            </li>
            <hr>
            <li class="nav-item mb-1">
                <a href="../topnav/nav.php" class="nav-link text-white"><i class="bi bi-shop text-warning"></i> Shop</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right text-danger"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #5b92e5;">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to logout?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="../other-functions/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div id="main-content" class="container account-wrapper">

</div>


    <!-- Toast Message -->
    <div id="adminToast" class="position-fixed start-50 translate-middle-x mt-4 d-none" style="top: 20px; z-index: 9999;">
        <div id="toastAlert" class="alert alert-<?php echo $toast_type; ?> text-center fade show" role="alert">
            <strong id="toastMessage"><?php echo $toast_message; ?></strong>
        </div>
    </div>

    <script src="../other-functions/bootstrap.bundle.min.js"></script>
	
	

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById("toggleSidebarBtn");
    const sidebar = document.getElementById("sidebar");
    const wrapper = document.getElementById("pageWrapper");

    toggleBtn?.addEventListener("click", function (e) {
        e.stopPropagation();
        sidebar.classList.toggle("active");
        toggleBtn.classList.toggle("active");
        wrapper.classList.toggle("sidebar-active");
    });

    const links = document.querySelectorAll('#sidebar-nav .nav-link');

    function loadContent(page, clickedLink) {
        fetch(page)
            .then(res => {
                if (!res.ok) throw new Error("HTTP error " + res.status);
                return res.text();
            })
            .then(html => {
                document.getElementById('main-content').innerHTML = html;
                links.forEach(link => link.classList.remove('active'));
                clickedLink.classList.add('active');
            })
            .catch(err => {
                document.getElementById('main-content').innerHTML = '<div class="p-5 text-center text-danger">Error loading content.</div>';
                console.error("Error loading page:", err);
            });
    }

    links.forEach(link => {
        link.addEventListener('click', function (e) {
            const page = this.getAttribute('data-page');
            const customPage = this.getAttribute('data-href');

            if (page || customPage) {
                e.preventDefault();
                loadContent(page || customPage, this);
            }
        });
    });

    // Load default page if marked active
    const defaultPage = document.querySelector('#sidebar-nav .nav-link.active');
    if (defaultPage) {
        const page = defaultPage.getAttribute('data-page') || defaultPage.getAttribute('data-href');
        if (page) {
            loadContent(page, defaultPage);
        }
    }

    // Toast message
    const toastMessage = "<?php echo $toast_message; ?>".trim();
    if (toastMessage !== "") {
        const toastEl = document.getElementById("adminToast");
        toastEl.classList.remove("d-none");
        setTimeout(() => {
            toastEl.classList.add("d-none");
        }, 3000);
    }
});
</script>



    </script>
	<script>
    // Delegate click event for dynamically loaded Edit button
    document.addEventListener('click', function (e) {
        if (e.target.closest('a[onclick="enableEdit()"]')) {
            e.preventDefault();
            const form = document.querySelector('form');
            if (!form) return;

            const inputs = form.querySelectorAll('input:not([disabled])');
            inputs.forEach(input => input.removeAttribute('readonly'));
            const saveBtn = document.getElementById('saveBtn');
            if (saveBtn) saveBtn.disabled = false;
        }
    });
</script>

</div>
</body>
</html>
