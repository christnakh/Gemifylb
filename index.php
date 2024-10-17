<?php
session_start(); // Start the session

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gemify</title>  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
  <link href="https://fonts.googleapis.com/css2?family=Morina:wght@400;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="css/global.css">
  <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">

  <!-- Favicon -->
  <link rel="icon" href="./images/favicon.ico" type="image/x-icon">
  <!-- Meta Description -->
  <meta name="description" content="GemifyLB is your premier jewelry marketplace where you can discover and post an exquisite collection of jewelry, watches, gadgets, diamonds, gemstones, and more. Join our community and find unique treasures or sell your own!">
  <!-- Meta KeyWoards -->
  <meta name="keywords" content="jewelry marketplace, buy jewelry, sell jewelry, watches, gadgets, diamonds, gemstones, fine jewelry, luxury items, handmade jewelry, jewelry auction, GemifyLB, custom jewelry, vintage jewelry, fashion jewelry, engagement rings, wedding bands, bracelets, necklaces, earrings, gemstones for sale, jewelry designers, jewelry appraisals, jewelry repair, precious metals, jewelry collectors, online jewelry shop, jewelry sales, jewelry trends, luxury watches, accessories, personal accessories, stylish gadgets, artisan jewelry, unique gifts, estate jewelry">

  <style>
    .feedback .carousel-container {
    position: relative;
    overflow: hidden;
}

.carousel-items {
    display: flex;
    transition: transform 0.5s ease;
}

.item {
    min-width: 100%; /* Each item takes full width of the container */
    box-sizing: border-box;
    padding: 20px;
}

.carousel-nav-buttons {
    display: none; /* Hidden by default, shown only in mobile view */
    position: absolute;
    width: 100%;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    text-align: center;
}

.carousel-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .carousel-nav-buttons {
        display: block;
    }
}

  </style>

</head>

<body>
      <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../images/navbar-logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="pages/contactus.php">Contact us</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="pages/my_post.php">My post</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button">
                                Post
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="pages/post_diamonds.php">Diamonds</a></li>
                                <li><a class="dropdown-item" href="pages/post_black_diamonds.php">Black Diamonds</a></li>
                                <li><a class="dropdown-item" href="pages/post_gemstones.php">Gemstones</a></li>
                                <li><a class="dropdown-item" href="pages/post_jewerlys.php">Jewelry</a></li>
                                <li><a class="dropdown-item" href="pages/post_watches.php">Watches</a></li>
                                <li><a class="dropdown-item" href="pages/post_gadgets.php">Gadgets</a></li>
                            </ul>
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
                            <a class="nav-link text-white" href="pages/products.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../pages/notification.php" title="Notifications">
                                <i class="fas fa-bell"></i> <!-- Notification bell icon -->
                            </a>
                        </li>
                                      <li class="nav-item">
                            <a class="nav-link text-white" href="pages/favorites.php">
                                <i class="fas fa-heart"></i> <!-- Solid heart icon (bold) -->
                            </a>    
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="pages/profile.php">
                                <i class="fas fa-user"></i> <!-- Solid user icon (bold) -->
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="pages/logout.php">
                                <i class="fas fa-sign-out-alt"></i> <!-- Solid logout icon (bold) -->
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="pages/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero d-flex align-items-center justify-content-center position-relative vh-100">
      <div class="slide w-100 h-100 d-flex align-items-center justify-content-center position-relative overflow-hidden">
        <img src="images/landing.png" alt="Jewelry" class="slide-image h-100 object-cover" />
        <div class="text-center position-absolute text-white py-4">
          <h1 class="display-5 fw-bold text-shadow">JEWELLERY FOR THE FEMININE IN YOU</h1>
          <p class="lead text-shadow display-6">Take a moment and cherish the grand collection</p>
          <button class="btn btn-outline-light text-uppercase w-auto rounded-0 p-3">View Products</button>
        </div>
      </div>
    </section>


    <div class="blogs bg-light py-5">
      <div class="container">
        <h2 class="text-center mb-5">Our blogs</h2>

            <div class="row">
              <div class="col-12 col-md-4">
                <div class="blog-item position-relative overflow-hidden">
                  <img src="images/image2.png" alt="Blog Image" class="w-100 h-100 mb-2" />
                  <div class="blog-details p-2">
                    <!-- <p class="text-start"><i class="fas fa-solid fa-user"></i>Ramamoorthi M <span>|</span> <i class="fas fa-solid fa-calendar"></i>July 18, 2020</p> -->
                    <p class="text-start">Share your love with couple rings</p>
                    <p class="text-start">
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore. Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore
                    </p>
                    <a href="#" class="btn rounded-0 mb-3">Read More</a>
                  </div>
                </div>
              </div>
                <div class="col-12 col-md-4">
                <div class="blog-item position-relative overflow-hidden">
                  <img src="images/image2.png" alt="Blog Image" class="w-100 h-100 mb-2" />
                  <div class="blog-details p-2">
                    <!-- <p class="text-start"><i class="fas fa-solid fa-user"></i>Ramamoorthi M <span>|</span> <i class="fas fa-solid fa-calendar"></i>July 18, 2020</p> -->
                    <p class="text-start">Share your love with couple rings</p>
                    <p class="text-start">
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore. Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore
                    </p>
                    <a href="#" class="btn rounded-0 mb-3">Read More</a>
                  </div>
                </div>
              </div>
                           <div class="col-12 col-md-4">
                <div class="blog-item position-relative overflow-hidden">
                  <img src="images/image2.png" alt="Blog Image" class="w-100 h-100 mb-2" />
                  <div class="blog-details p-2">
                    <!-- <p class="text-start"><i class="fas fa-solid fa-user"></i>Ramamoorthi M <span>|</span> <i class="fas fa-solid fa-calendar"></i>July 18, 2020</p> -->
                    <p class="text-start">Share your love with couple rings</p>
                    <p class="text-start">
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore. Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore
                    </p>
                    <a href="#" class="btn rounded-0 mb-3">Read More</a>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="blog-item position-relative overflow-hidden">
                  <img src="images/image2.png" alt="Blog Image" class="w-100 h-100 mb-2" />
                  <div class="blog-details p-2">
                    <!-- <p class="text-start"> <i class="fas fa-solid fa-user"></i> Ramamoorthi M <span>|</span> <i class="fas fa-solid fa-calendar"></i>July 18, 2020</p> -->
                    <p class="text-start">Share your love with couple rings</p>
                    <p class="text-start">
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore. Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore
                    </p>
                    <a href="#" class="btn rounded-0 mb-3">Read More</a>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="blog-item position-relative overflow-hidden">
                  <img src="images/image2.png" alt="Blog Image" class="w-100 h-100 mb-2" />
                  <div class="blog-details p-2">
                    <!-- <p class="text-start"><i class="fas fa-solid fa-user"></i>Ramamoorthi M <span>|</span> <i class="fas fa-solid fa-calendar"></i>July 18, 2020</p> -->
                    <p class="text-start">Share your love with couple rings</p>
                    <p class="text-start">
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore. Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore
                    </p>
                    <a href="#" class="btn rounded-0">Read More</a>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="blog-item position-relative overflow-hidden">
                  <img src="images/image2.png" alt="Blog Image" class="w-100 h-100 mb-2" />
                  <div class="blog-details p-2">
                    <!-- <p class="text-start"><i class="fas fa-solid fa-user"></i>Ramamoorthi M <span>|</span> <i class="fas fa-solid fa-calendar"></i>July 18, 2020</p> -->
                    <p class="text-start">Share your love with couple rings</p>
                    <p class="text-start">
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore. Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                      Sint, dolore
                    </p>
                    <a href="#" class="btn rounded-0">Read More</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

    <!-- <div class="members">
      <h2 class="special-heading">Why our members love Gemify</h2>
      <div class="members-content">
        <div class="item bg-white rounded-1">
          <img src="images/jewellery.png" alt="" />
          <div class="item-details">
            <h3>Source and Sell Diamonds</h3>
            <p>
              Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
              consequuntur quam asperiores adipisci excepturi, accusantium
              aliquam eos nostrum natus eum!
            </p>
            <button>Learn more</button>
          </div>
        </div>
        <div class="item bg-white rounded-1">
          <img src="images/jewellery.png" alt="" />
          <div class="item-details">
            <h3>Source and Sell Diamonds</h3>
            <p>
              Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
              consequuntur quam asperiores adipisci excepturi, accusantium
              aliquam eos nostrum natus eum!
            </p>
            <button>Learn more</button>
          </div>
        </div>
        <div class="item bg-white rounded-1">
          <img src="images/jewellery.png" alt="" />
          <div class="item-details">
            <h3>Source and Sell Diamonds</h3>
            <p>
              Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
              consequuntur quam asperiores adipisci excepturi, accusantium
              aliquam eos nostrum natus eum!
            </p>
            <button>Learn more</button>
          </div>
        </div>
        <div class="item bg-white rounded-1">
          <img src="images/jewellery.png" alt="" />
          <div class="item-details">
            <h3>Source and Sell Diamonds</h3>
            <p>
              Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
              consequuntur quam asperiores adipisci excepturi, accusantium
              aliquam eos nostrum natus eum!
            </p>
            <button class="btn-learn-more">Learn more</button>
          </div>
        </div>
      </div>
    </div> -->

    <div class="feedback">
      <h2 class="special-heading">Why our members love Gemify</h2>
      <div class="carousel-container">
        <button class="carousel-btn prev-btn">&#10094;</button>
        <div class="carousel-items feedback-content">
          <div class="item">
            <div class="profile">
              <img src="images/jewellery.png" alt="" />
              <h4>Name Surname</h4>
            </div>
            <div class="item-details">
              <h5>Beirut, Lebanon</h5>
              <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
                consequuntur quam asperiores adipisci excepturi, accusantium
                aliquam eos nostrum natus eum! Lorem, ipsum dolor sit amet
                consectetur adipisicing elit. Quasi consequuntur quam asperiores
                adipisci excepturi, accusantium aliquam eos nostrum natus eum!
              </p>
              <h5>7th of July 2024</h5>
            </div>
          </div>
          <div class="item">
            <div class="profile">
              <img src="images/jewellery.png" alt="" />
              <h4>Name Surname</h4>
            </div>
            <div class="item-details">
              <h5>Beirut, Lebanon</h5>
              <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
                consequuntur quam asperiores adipisci excepturi, accusantium
                aliquam eos nostrum natus eum! Lorem, ipsum dolor sit amet
                consectetur adipisicing elit. Quasi consequuntur quam asperiores
                adipisci excepturi, accusantium aliquam eos nostrum natus eum!
              </p>
              <h5>7th of July 2024</h5>
            </div>
          </div>
          <div class="item">
            <div class="profile">
              <img src="images/jewellery.png" alt="" />
              <h4>Name Surname</h4>
            </div>
            <div class="item-details">
              <h5>Beirut, Lebanon</h5>
              <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi
                consequuntur quam asperiores adipisci excepturi, accusantium
                aliquam eos nostrum natus eum! Lorem, ipsum dolor sit amet
                consectetur adipisicing elit. Quasi consequuntur quam asperiores
                adipisci excepturi, accusantium aliquam eos nostrum natus eum!
              </p>
              <h5>7th of July 2024</h5>
            </div>
          </div>
          </div>

          
          <br>
          <br>


        <!-- </div>
        <button class="carousel-btn next-btn">&#10095;</button>
      </div> -->
<!-- 
      <div class="feedback-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div> -->

    <div class="quote">
      <div class="container">
        <h2>Join Gemify</h2>
        <p>
          The world's largest and most trusted marketplace for diamonds and
          Jewelry!
        </p>
        <div class="btns">
          <button>Register</button>
          <button>Login</button>
        </div>
      </div>
    </div>

    <footer class="footer">
      <div class="container">
        <div class="row footer-content">
          <div class="col-md-3 col-sm-6">
            <h5 class="mt-3">Follow Us</h5>
            <ul>
              <li><i class="fab fa-twitter"></i>Twitter</li>
              <li><i class="fab fa-facebook-f"></i>Facebook</li>
              <li><i class="fab fa-instagram"></i>Instagram</li>
            </ul>
          </div>
          <div class="col-md-3 col-sm-6">
            <h5 class="mt-3">Information</h5>
            <ul>
              <li>Advanced Search</li>
              <li>Search Terms</li>
              <li>Help & FAQ's</li>
              <li>Store Location</li>
              <li>Order & Return</li>
            </ul>
          </div>
          <div class="col-md-3 col-sm-6">
            <h5 class="mt-3">Support</h5>
            <ul>
              <li>e-Mail Support</li>
              <li>Terms Of Delivery</li>
              <li>Refund & Return</li>
              <li>Privacy Policy</li>
              <li>Chat Support</li>
            </ul>
          </div>
          <div class="col-md-6 col-sm-6">
            <h5 class="mt-3">Contact Us</h5>
            <ul>
              <li><i class="fas fa-home"></i>Lorem ipsum dolor sit amet consectetur</li>
              <li><i class="fas fa-solid fa-phone"></i>031234567789087</li>
              <li><i class="fas fa-solid fa-envelope"></i>info@fhjk.com</li>
              <li><i class="fas fa-solid fa-clock"></i>9:00 AM to 6:00 PM</li>
            </ul>
          </div>
          <div class="col-md-6 col-sm-6">
            <h5 class="mt-3">Help</h5>
            <ul>
              <li><a href="">Shipping</a></li>
              <li><a href="">Returns</a></li>
              <li><a href="">Careers</a></li>
              <li><a href="">FAQ</a></li>
              <li><a href="">Contact Us</a></li>
            </ul>
          </div>
        </div>
        <div class="row">
          <div class="col-12 text-start">
            <hr class="w-50">
            <p>&copy; 2024 <a href="index.php" class="copyright">GEMIFY</a>. All rights reserved.</p>
          </div>
        </div>
      </div>
    </footer>
<!-- 
    <script>
      // Array of image sources
const images = [
  'images/landing1.png',
  // Add more image paths as needed
];

let currentImageIndex = 0;

function changeImage() {
  // Get the image element
  const imageElement = document.querySelector('.slide-image');

  // Update the image source
  currentImageIndex = (currentImageIndex + 1) % images.length;
  imageElement.src = images[currentImageIndex];
}

// Call the function every 5 seconds
setInterval(changeImage, 5000); // 5000 milliseconds = 5 seconds

    </script> -->
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


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelector('.carousel-items');
    let currentIndex = 0;

    document.querySelector('.prev-btn').addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            items.style.transform = `translateX(-${currentIndex * 100}%)`;
        }
    });

    document.querySelector('.next-btn').addEventListener('click', function() {
        if (currentIndex < items.children.length - 1) {
            currentIndex++;
            items.style.transform = `translateX(-${currentIndex * 100}%)`;
        }
    });
});

</script>

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>