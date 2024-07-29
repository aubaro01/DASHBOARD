<?php
require 'php/config.php'; // Inclui a configuração da sessão

require_once 'php/db.php';
require_once 'php/funcs.php'; 

$db = new DB();

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        if (checkCredentials($db, $email, $password)) {
            session_regenerate_id(true); // Regenera o ID da sessão
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;

            // Obter o número do utilizador com base no email
            $sql = "SELECT USER_id FROM user WHERE USER_email = ?";
            $args = array($email);
            $result = $db->send2db($sql, $args);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['USER_id'] = $row['USER_id'];
            }

            header('Location: php/dash.php');
            exit;
        } else {
            $error_message = "Credenciais inválidas. Por favor, tente novamente.";
        }
    } else {
        $error_message = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DashPro</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="img/logo_icon.png">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <a href="#" class="nav_logo">DashPro</a>
            <ul class="nav_items">
                <li class="nav_item">
                    <a href="#home" class="nav_link">Home</a>
                    <a href="#Product" class="nav_link">Product</a>
                    <a href="#conctat" class="nav_link">Contact</a>
                </li>
            </ul>
            <button class="button" id="form-open">Login</button>
        </nav>
    </header>

    <!-- Home -->
    <section class="home">
    <div class="form_container">
        <i class="uil uil-times form_close"></i>
        <!-- Login Form -->
        <div class="form login_form">
            <form action="index.php" method="post">
                <h2>Login</h2>
                <div class="input_box">
                    <input type="email" name="email" placeholder="Enter your email" required />
                    <i class="uil uil-envelope-alt email"></i>
                </div>
                <div class="input_box">
                    <input type="password" name="password" placeholder="Enter your password" required />
                    <i class="uil uil-lock password"></i>
                    <i class="uil uil-eye-slash pw_hide"></i>
                </div>
                <div class="option_field">
                    <span class="checkbox">
                        <input type="checkbox" id="check" />
                        <label for="check">Remember me</label>
                    </span>
                </div>
                <button class="button">Login Now</button>
            </form>
            <?php
            if (!empty($error_message)) {
                echo '<div class="error-message" style="color: red; text-align: left; font-size: 12px;">' . $error_message . '</div>';
            }
            ?>
        </div>
    </div>
      
      <section id="home" class="hero">
        <div class="hero-text">
          <h2>DashPro: Your Data, Your Dashboard, Your Decision</h2>
          <a href="#conctat" class="button">Get Started <i class="uil uil-arrow-right"></i></a>
        </div>
      </section>
    </section>
    <section>
    </section>

    
    <main class="main-content">

      <section id="Product">
      <div class="text_prod">
        <p><strong>PRODUCT</strong></p>
      </div>
      <img class="dashboard-image"src="img/dash.jpg" alt="Dashboard exemplo">
    </section>

    <div class="container_2" style="margin-top: 150px;">
        <div class="text-left">
            <div class="price_text">We have what you need</div>
            <p>"Why choose DashPro? With our platform, you simplify data management and improve decision-making with accurate, real-time information. Our affordable plans are designed to meet your specific needs."</p>
        </div>
        <div class="wrapper">
            <div class="card-area">
                <div class="cards">
                    <div class="row row-1">
                        <div class="price-details">
                            <span class="price">19</span>
                            <p>For all use</p>
                        </div>
                        <ul class="features">
                            <li><i class="uil uil-check"></i><span>CUSTOM MANAGEMENT TABLES</span></li>
                            <li><i class="uil uil-check"></i><span>TECHNICAL SUPPORT</span></li>
                            <li><i class="uil uil-check"></i><span>REAL-TIME DATA MANIPULATION AND VISUALIZATION</span></li>
                            <li><i class="uil uil-check"></i><span>EXCLUSIVE EMAIL ACCOUNTS AND DATABASES</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <button href="#contact">ACQUIRE</button>
        </div>
    </div>
<!--Contacto-->
<section id="contact">
        <div class="text_cont">
            <p><strong>Contact Us</strong></p>
        </div>
        <div class="container">
            <div class="content">
                <div class="left-side">
                    <div class="address details">
                        <i class="uil uil-map-marker"></i>
                        <div class="topic">Address</div>
                        <div class="text-one">R. de Silva Tapada 115</div>
                        <div class="text-two">Porto</div>
                    </div>
                    <div class="phone details">
                        <i class="uil uil-phone"></i>
                        <div class="topic">Number</div>
                        <div class="text-one">+351 9876 5432</div>
                    </div>
                    <div class="email details">
                        <i class="uil uil-envelope"></i>
                        <div class="topic">Email</div>
                        <div class="text-one">exemple@gmail.com</div>
                    </div>
                </div>
                <div class="right-side">
                    <div class="topic-text">Contact Us</div>
                    <p>We can help you?</p>
                    <form action="/php/Contact.php" method="POST">
                        <div class="input-box">
                            <input type="text" name="name" placeholder="Name" required>
                        </div>
                        <div class="input-box">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="input-box message-box">
                            <textarea name="message" placeholder="Message" required></textarea>
                        </div>
                            <button class="button" type="submit">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer"> 
    <ul class="menu">
      <li class="menu__item"><a class="menu__link" href="#home">Home</a></li>
      <li class="menu__item"><a class="menu__link" href="#Product">Product</a></li>
      <li class="menu__item"><a class="menu__link" href="#contact">Contact</a></li>
    </ul>
    <p>&copy;2024 DashPro | All Rights Reserved</p>
    </footer>

    </main>


    <script>    
      const formOpenBtn = document.querySelector("#form-open"),
        home = document.querySelector(".home"),
        formContainer = document.querySelector(".form_container"),
        formCloseBtn = document.querySelector(".form_close"),
        signupBtn = document.querySelector("#signup"),
        loginBtn = document.querySelector("#login"),
        pwShowHide = document.querySelectorAll(".pw_hide");
      
      formOpenBtn.addEventListener("click", () => home.classList.add("show"));
      formCloseBtn.addEventListener("click", () => home.classList.remove("show"));
      
      pwShowHide.forEach((icon) => {
        icon.addEventListener("click", () => {
          let getPwInput = icon.parentElement.querySelector("input");
          if (getPwInput.type === "password") {
            getPwInput.type = "text";
            icon.classList.replace("uil-eye-slash", "uil-eye");
          } else {
            getPwInput.type = "password";
            icon.classList.replace("uil-eye", "uil-eye-slash");
          }
        });
      });
      
      signupBtn.addEventListener("click", (e) => {
        e.preventDefault();
        formContainer.classList.add("active");
      });
      loginBtn.addEventListener("click", (e) => {
        e.preventDefault();
        formContainer.classList.remove("active");
      });

      // script.js

      /*window.addEventListener('scroll', function() {
        const header = document.querySelector('.header');
        const scrollPosition = window.scrollY;

        if (scrollPosition > 50) { // Define a quantidade de rolagem antes de mudar a cor
          header.classList.add('scrolled');
        } else {
          header.classList.remove('scrolled');
        }
      });*/

      // script.js
      document.querySelectorAll('.navbar a').forEach(anchor => {
          anchor.addEventListener('click', function (e) {
              e.preventDefault();

              const targetId = this.getAttribute('href').substring(1);
              const targetSection = document.getElementById(targetId);

              window.scrollTo({
                  top: targetSection.offsetTop - 50, // ajuste de acordo com a altura da navbar
                  behavior: 'smooth'
              });
          });
      });

      document.getElementById('toggle-bg').addEventListener('click', function() {
        const overlay = document.getElementById('overlay');
        overlay.classList.toggle('active');
      });

    


    </script>
  </body>
</html>
