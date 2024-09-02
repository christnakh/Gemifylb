<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
    font-family: Arial, sans-serif;
}

.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    height: calc(100vh - 48px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.nav-link.active {
    color: #007bff;
}

.nav-link {
    font-size: 1.1rem;
    padding: 15px;
}

.nav-link i {
    margin-right: 10px;
}

.header {
    margin-bottom: 20px;
}

.container .row .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    padding: 20px;
}

.footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #f8f9fa;
}

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h4 class="text-center my-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#user-management">
                                <i class="fas fa-users"></i> User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#post-management">
                                <i class="fas fa-newspaper"></i> Post Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#rapaport-management">
                                <i class="fas fa-file-alt"></i> Rapaport Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact-us-management">
                                <i class="fas fa-envelope"></i> Contact Us Management
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </header>

                <div class="container my-5">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <a href="user_management.php" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-users"></i> User Management
                            </a>
                        </div>
                        <div class="col-md-6 mb-4">
                            <a href="post_management.php" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-newspaper"></i> Post Management
                            </a>
                        </div>
                        <div class="col-md-6 mb-4">
                            <a href="rapaport_management.php" class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-file-alt"></i> Rapaport Management
                            </a>
                        </div>
                        <div class="col-md-6 mb-4">
                            <a href="contact_us_management.php" class="btn btn-danger btn-lg btn-block">
                                <i class="fas fa-envelope"></i> Contact Us Management
                            </a>
                        </div>
                    </div>
                </div>
                
                <footer class="footer mt-auto py-3 bg-light">
                    <div class="container text-center">
                        <span class="text-muted">&copy; 2024 Admin Dashboard</span>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
