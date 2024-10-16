<?php
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Morina:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/global.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../images/navbar-logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="contactus.php">Contact us</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="my_post.php">My post</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button">
                                Post
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="post_diamonds.php">Diamonds</a></li>
                                <li><a class="dropdown-item" href="post_black_diamonds.php">Black Diamonds</a></li>
                                <li><a class="dropdown-item" href="post_gemstones.php">Gemstones</a></li>
                                <li><a class="dropdown-item" href="post_jewerlys.php">Jewelry</a></li>
                                <li><a class="dropdown-item" href="post_watches.php">Watches</a></li>
                                <li><a class="dropdown-item" href="post_gadgets.php">Gadgets</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button">
                                Rapaport/ Gold
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="../uploads/rapaport/pear.pdf">Pear</a></li>
                                <li><a class="dropdown-item" href="../uploads/rapaport/round.pdf">Round</a></li>
                                 <li><a class="dropdown-item" href="#" id="gold-price-link">Gold</a></li>
                            </ul>
                        </li>
               
                        <li class="nav-item">
                            <a class="nav-link text-white" href="products.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../pages/notification.php" title="Notifications">
                                <i class="fas fa-bell"></i> <!-- Notification bell icon -->
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="favorites.php">
                                <i class="fas fa-heart"></i> <!-- Solid heart icon (bold) -->
                            </a>    
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="profile.php">
                                <i class="fas fa-user"></i> <!-- Solid user icon (bold) -->
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> <!-- Solid logout icon (bold) -->
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- <li class="nav-item">
                            <a class="nav-link text-white" href="login.php">Login</a>
                        </li> -->
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Ensures dropdown works on hover by adding the necessary Bootstrap classes
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown'))
        dropdownElementList.map(function (dropdown) {
            dropdown.addEventListener('mouseover', function () {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.add('show');
                }
            });
            dropdown.addEventListener('mouseout', function () {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });
    </script>
</body>


<script>
    document.getElementById('gold-price-link').addEventListener('click', function (e) {
      e.preventDefault();  // Prevent default link behavior

      // Fetch gold price from goldapi.io (replace YOUR-API-KEY with actual key)
      fetch('https://www.goldapi.io/api/XAU/USD', {
        headers: {
          'x-access-token': 'goldapi-3qag3sm2bn7lav-io',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        const goldPrice = data.price;
        alert(`The current gold price is $${goldPrice} per ounce.`);
      })
      .catch(error => {
        console.error('Error fetching gold price:', error);
        alert('Failed to retrieve gold price.');
      });
    });
  </script>
</html>
