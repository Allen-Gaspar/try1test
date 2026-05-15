<?php
session_start();
session_unset();
session_destroy();

// back to login
echo "<script>window.open('../other-functions/login.php', '_self');</script>";
exit();

?>

<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.open('../other-functions/login.php', '_self');</script>";
    exit();
}

$toast_message = isset($_SESSION['toast_message']) ? $_SESSION['toast_message'] : "";
$toast_type = isset($_SESSION['toast_type']) ? $_SESSION['toast_type'] : "info";

unset($_SESSION['toast_message']);
unset($_SESSION['toast_type']);

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
            padding-top: 100px;
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .fixed-header {
            background-color: #E4ADFF;
            font-size: 0.875rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1050;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .top-banner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px 15px;
            font-weight: bold;
        }

        .navbar {
            background-color: #7388FE;
            padding: 12px 20px;
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

        .container {
            padding: 30px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 1.2rem;
        }

        .card-body i {
            font-size: 2rem;
            color: #7388FE;
        }
		.sidebar {
    width: 250px;
    transition: all 0.3s ease;
}

.sidebar.collapsed {
    width: 0;
    padding: 0;
    overflow: hidden;
}

.main-content {
    margin-left: 250px;
    transition: all 0.3s ease;
}

.main-content.collapsed {
    margin-left: 0;
}

    </style>
</head>
<body>

    <div class="fixed-header">
		<div class="d-flex">
    <!-- Sidebar -->
    <nav id="sidebar" class="bg-dark text-white p-3 sidebar" style="height: 100vh; position: fixed; top: 70px; left: 0; overflow-y: auto;">

        <div class="mb-4">
            <h4 class="text-white">ADMINDek</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-house"></i> Dashboard
                </a>
                <ul class="nav flex-column ms-3">
                    <li class="nav-item"><a href="#" class="nav-link text-white-50">Default</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white-50">CRM</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white-50">Analytics <span class="badge bg-info text-dark">new</span></a></li>
                </ul>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-layout-text-window-reverse"></i> Page Layouts <span class="badge bg-warning text-dark">new</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-menu-button"></i> Navigation
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-box"></i> Widget <span class="badge bg-danger">100+</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Content -->
<div id="mainContent" class="main-content">
        <!-- Your existing content starts here -->

	
        <div class="top-banner">
            <i class="bi bi-gem me-2" style="color: #06b5dd;"></i> Welcome, Admin!
        </div>

        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
				<button class="btn btn-outline-light me-3" type="button" id="toggleSidebarBtn">
    <i class="bi bi-list"></i>
</button>

                <a class="navbar-brand fw-bold text-white" href="#">
                    <span class="fs-4">Admin Panel</span>
                </a>
				
				
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout2.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../topnav/nav.php">
                            <i class="bi bi-house"></i> Home	
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container">
        <div class="row mt-3">
		
            <div class="col-md-4">
                <div class="card p-3">
                    <div class="card-body">
                        <i class="bi bi-people-fill"></i>
                        <h5 class="card-title mt-3">Registered Users</h5>
                        <p class="card-text">View all registered users from the shop with ease.</p>
                        <a href="manage_users.php" class="btn btn-primary btn-sm">View Users</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-line-fill"></i>
                        <h5 class="card-title mt-3">Sales Overview</h5>
                        <p class="card-text">View and track the sales statistics and growth.</p>
                        <a href="sales_report.php" class="btn btn-primary btn-sm">View Sales</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3">
                    <div class="card-body">
                        <i class="bi bi-clock-history"></i>
                        <h5 class="card-title mt-3">Recent Activity</h5>
                        <p class="card-text">Monitor and view the latest user actions.</p>
                        <a href="activity_log.php" class="btn btn-primary btn-sm">View Activity</a>
                    </div>
                </div>
            </div>
			
			<div class="col-md-4 mt-4">
                <div class="card p-3">
                    <div class="card-body">
                        <i class="bi bi-box-seam"></i>
                        <h5 class="card-title mt-3">About Products</h5>
                        <p class="card-text">Add, update and delete products with ease.</p>
                        <a href="activity_log.php" class="btn btn-primary btn-sm">Manage Products</a>
                    </div>
                </div>
            </div>
        </div>
		
	<hr div class="row mt-5">

        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card p-4">
                    <div class="card-body">
                        <h5 class="card-title">Quick Links</h5>
                        <ul class="list-inline">
                            <li class="list-inline-item"><a href="settings.php" class="btn btn-outline-secondary btn-sm">Settings</a></li>
                            <li class="list-inline-item"><a href="reports.php" class="btn btn-outline-secondary btn-sm">Reports</a></li>
                            <li class="list-inline-item"><a href="support.php" class="btn btn-outline-secondary btn-sm">Support</a></li>
                            <li class="list-inline-item"><a href="notifications.php" class="btn btn-outline-secondary btn-sm">Notifications</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
	
	<div id="adminToast" class="position-fixed start-50 translate-middle-x mt-4 d-none" style="top: 20px; z-index: 9999;">
    <div id="toastAlert" class="alert alert-<?php echo $toast_type; ?> text-center fade show" role="alert" style="transition: opacity 0.5s;">
        <strong id="toastMessage"><?php echo $toast_message; ?></strong>
    </div>
</div>
    </div> <!-- End flex-grow-1 -->
</div> <!-- End d-flex -->


<script src="../other-functions/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let toastMessage = "<?php echo addslashes($toast_message); ?>";

        if (toastMessage.trim() !== "") {
            const toastWrapper = document.getElementById("adminToast");
            const toastAlert = document.getElementById("toastAlert");
            const toastText = document.getElementById("toastMessage");

            toastText.innerText = toastMessage;
            toastWrapper.classList.remove("d-none");

            setTimeout(() => {
                toastAlert.classList.remove("show"); 
                toastAlert.classList.add("opacity-0");

                setTimeout(() => {
                    toastWrapper.classList.add("d-none");
                }, 500); 
            }, 3000);
        }
    });
</script>

	<script>
    document.getElementById("toggleSidebarBtn").addEventListener("click", function () {
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("mainContent");

        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("collapsed");
    });
</script>

	
</body>
</html>
