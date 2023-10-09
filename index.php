<?php
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';
?>
    <meta property="og:image" content="https://ykinnovate.com/images/seo.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="description" content="'Web Development,''IT Management,' 'Technology Solutions,' 'social media,' and  graphic design are examples of the services we offer.">
    <meta name="keywords" content="web development, IT management, technology solutions, responsive web design, database management, software development, desktop applications, IT support, technology consulting, web developer, IT services, Austria, YK-Technology, social media, graphic design">
    <meta property="og:title" content="YK-technology">
    <meta property="og:url" content="https://www.ykinnovate.com">
    <meta property="og:description" content="'Web Development,''IT Management,' 'Technology Solutions,' 'social media,' and  graphic design are examples of the services we offer.">
    <meta property="og:type" content="website">
    
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="index.css?v=1.5">
</head>
<body>
    <header>
        <div class="companyinfo">
            <img src="images/logo.png" alt="">
            <h3>YK-Technology</h3>
        </div>
        <input type="checkbox" id="menu-bar">
        <label for="menu-bar"><i class="fa-solid fa-list-ul"></i></label>
        <nav class="navbar">
            <ul>
                <li> <a href="pricing.php"> <i class="fa-solid fa-money-bill-1"></i> Pricing</a></li>
                <li> <a href="#services"> <i class="fa-solid fa-code"></i> Servicers</a></li>
                <li><a href="#how_we_work"> <i class="fa-solid fa-briefcase"></i> How we Work</a></li>
                <li><a href="#contact_us"> <i class="fa-solid fa-phone"></i> Contact Us</a></li>
                <li><a href="user/"> <i class="fa-solid fa-user-tie"></i> Login</a></li>
            </ul>
        </nav>
    </header>
    <div class="slideshow-container">
        <div class="slideshow-content" id="slideshow">
            <?php
                $sql = $con->prepare('SELECT slideimg FROM tblslideshow WHERE slideactive = 1');
                $sql->execute();
                $count = $sql->rowCount();
                $result = $sql->fetchAll();
                foreach($result as $slide){
                    echo '
                        <div class="mySlides">
                            <img src="images/slideshow/'.$slide['slideimg'].'" alt="Image">
                        </div>
                    ';
                }
                // Duplicate the images to ensure continuous circular looping
                foreach($result as $slide){
                    echo '
                        <div class="mySlides">
                            <img src="images/slideshow/'.$slide['slideimg'].'" alt="Image">
                        </div>
                    ';
                }
            ?>
        </div>
    </div>
    <div class="services" id="services">
        <h1>Our Services</h1>
        <p>Welcome to YK Technology, where we provide top-quality services tailored to your needs. Let us know what you require, and we'll take care of the rest.</p>
        <div class="allservice">
            <?php
                $sql=$con->prepare('SELECT Cat_ID,Category_Icon,Category_Name,Cat_Discription FROM tblcategory WHERE Cat_Active=1');
                $sql->execute();
                $Services=$sql->fetchAll();
                foreach($Services as $service){
                    echo '
                        <div class="card_service" data-index="'.$service['Cat_ID'].'">
                            <img src="images/Services/'.$service['Category_Icon'].'" alt="">
                            <div class="dis">
                                <h5>'.$service['Category_Name'].'</h5>
                                <label for="">'.$service['Cat_Discription'].'</label>
                            </div>
                        </div>
                    ';
                }
            ?>
        </div>
    </div>
    <div class="how_we_work" id="how_we_work">
        <h1>How We Work</h1>
        <p>We work by understanding your needs, planning and proposing a solution, and then designing and developing it.</p>
        <div class="how_we_work_cards">
            <?php
                $sql=$con->prepare('SELECT * FROM  tblhowwework ');
                $sql->execute();
                $actions=$sql->fetchAll();
                foreach($actions as $action){
                    echo '
                        <div class="card_work card'.$action['No'].'">
                            <h1>'.$action['No'].'</h1>
                            <p> <span style="font-weight: bold;">'.$action['title'].' :</span> '.$action['discription'].'</p>
                        </div>
                    ';
                }
            ?>
        </div>
        <p>Overall, our goal is to ensure that you are satisfied with the final product and that it meets your needs and goals. We achieve this by prioritizing communication and collaboration throughout the entire process.</p>
    </div>
    <div class="mycv">
        <h1>The Person Behind the Screen</h1>
        <?php
            $sql=$con->prepare('SELECT Cv_text,Cv_pic FROM  tblsetting WHERE SettingID = 1');
            $sql->execute();
            $cv=$sql->fetch();
        ?>
        <div class="dicription">
            <div class="text">
                <?php echo nl2br($cv['Cv_text']) ?>
            </div>
            <div class="img">
                <img src="images/synpoles/<?php echo $cv['Cv_pic'] ?>" alt="">
            </div>
        </div>
    </div>
    <?php
        $sql=$con->prepare('SELECT show_profolio FROM  tblsetting WHERE SettingID =1');
        $sql->execute();
        $showpro=$sql->fetch();
        if($showpro['show_profolio'] == 1){
            $displayPro = 'block';
        }else{
            $displayPro = 'none';
        }
    ?>
    <div class="my_portfolio" style="display: <?php echo  $displayPro ?>;">
        <h1>Showcasing My Skills and Experience</h1>
        <p>Explore my portfolio to see my expertise in web and desktop development, as well as my proficiency in IT management.</p>
        <div class="portfolio_cards">
            <?php
                $sql=$con->prepare('SELECT portfolio_ID,portfolio_Title,portfolio_Pic FROM tblportfolio WHERE portfolio_Active =1');
                $sql->execute();
                $portfolios=$sql->fetchAll();
                foreach($portfolios as $port){
                    echo '
                        <div class="port_card" data-index="'.$port['portfolio_ID'].'">
                            <img src="images/Profolio/'.$port['portfolio_Pic'].'" alt="">
                            <label for="" style="font-weight: bold;">'.$port['portfolio_Title'].'</label>
                        </div>
                    ';
                }
            ?>
        </div>
    </div>
    <?php
        $sql=$con->prepare('SELECT show_ourteam FROM  tblsetting WHERE SettingID =1');
        $sql->execute();
        $showworker=$sql->fetch();
        if($showworker['show_ourteam'] == 1){
            $displayworker = 'block';
        }else{
            $displayworker = 'none';
        }
    ?>
    <div class="ourteam" style="display: <?php echo  $displayworker ?>;">
        <h2>Our Team</h2>
        <div class="set_team">
            <?php
                $sql=$con->prepare('SELECT * FROM tblourworkers');
                $sql->execute();
                $workers = $sql->fetchAll();
                foreach ($workers as $per){
                    echo '
                    <div class="card_person">
                        <div class="img_person">
                            <img src="images/ourteam/'.$per['workerimg'].'" alt="">
                        </div>
                        <div class="workerinfo">
                            <h3>'.$per['workerName'].'</h3>
                            <label>'.$per['workerDiscription'].'</label>
                        </div>
                    </div>
                    ';
                }
            ?>

        </div>
    </div>
    <div class="contact_us" id="contact_us">
        <h2>Contact US</h2>
        <p>Our team is always available to answer any questions or concerns you have via our website or email.</p>
        <form action="" method="post">
            <div class="forsend">
                <div class="personalinfo">
                    <label for="">Name: </label>
                    <input type="text" name="txtname" id="" required>
                    <label for="">Phone Number:</label>
                    <input type="text" name="txtphonenumber" id="">
                    <label for="">E-mail</label>
                    <input type="email" name="txtemail" id="" required>
                </div>
                <div class="message">
                    <label for="">Message:</label>
                    <textarea name="txtmail" id="" cols="20" rows="6" required></textarea>
                </div>
            </div>
            <button type="submit" name="btnsentemail">Send</button>
        </form>
        <?php
            if(isset($_POST['btnsentemail'])){
                $ClientName=$_POST['txtname'];
                $phonenumber=$_POST['txtphonenumber'];
                $email=$_POST['txtemail'];
                $message=$_POST['txtmail'];
                require_once 'mail.php';
                $mail->setFrom($applicationemail, 'Contact US Form');
                $mail->addAddress('yehiakobeyssy2018@gmail.com');
                $mail->Subject = 'New Question From MR/Mis: ' . $ClientName;
                $mail->Body    = $message. '<br> <span style="font-weight: bold;"> The clinet Phone Number is</span>   :' . $phonenumber . '<br> <span style="font-weight: bold;"> The clinet Email is</span> : '. $email;
                $mail->send();
            }
        ?>
    </div>
    <footer>
        <?php
            $sql=$con->prepare('SELECT textaboutus,link_facebook,link_github,link_linkin FROM tblsetting WHERE  SettingID=1');
            $sql->execute();
            $aboutus=$sql->fetch();
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h3>About Us</h3>
                    <p><?php echo $aboutus['textaboutus'] ?></p>
                </div>
                <div class="col-md-3">
                    <h3>Contact Us</h3>
                    <p>Email: info@ykinnovate.com</p>
                    <p>Phone: +34 680764789</p>
                </div>
                <div class="col-md-3 follow">
                    <h3>Follow Us</h3>
                    <a href="<?php echo $aboutus['link_facebook'] ?>">Facebook</a>
                    <a href="<?php echo $aboutus['link_linkin'] ?>">LinkedIn</a>
                    <a href="<?php echo $aboutus['link_github'] ?>">GitHup</a>
                    <a href="terms.php">Policy and Terms of Service</a>
                </div>
                <div class="col-md-3 follow">
                    <h3>Language:</h3>
                    <span id="google_element"></span>
                    <a href="Saleagent/index.php">Sale Agent section</a>
                </div>
            </div>
        </div>
    </footer>
    <?php include 'common/jslinks.php'?>
    <script src="index.js"></script>
    <script>
        var slideshow = document.getElementById("slideshow");
        var slides = document.querySelectorAll(".mySlides");
        var slideIndex = 0;
        var transitionInProgress = false;
        var direction = 1; 

        showSlides();

        function showSlides() {
            if (!transitionInProgress) {
                var translateValue = -slideIndex * 100;
                slideshow.style.transition = "transform 4s ease-in-out"; 
                slideshow.style.transform = `translateX(${translateValue}%)`;

                
                setTimeout(function () {
                    transitionInProgress = true;

                    slideIndex += direction;

                    if (slideIndex >= slides.length) {
                        slideIndex = slides.length - 1;
                        direction = -1;
                    } else if (slideIndex < 0) {
                        slideIndex = 0;
                        direction = 1;
                    }

                    slideshow.style.transition = "none";

                    
                    setTimeout(function () {
                        transitionInProgress = false;
                        showSlides();
                    }, 0);
                }, 6000); 
            }
        }

        
        slideshow.addEventListener("transitionend", function () {
            transitionInProgress = false;
        });
    </script>
</body>