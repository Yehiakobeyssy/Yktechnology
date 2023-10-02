<?php
    include 'settings/connect.php';
    include 'common/function.php';
    include 'common/head.php';
?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="index.css">
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
                <li> <a href="pricing.html"> <i class="fa-solid fa-money-bill-1"></i> Pricing</a></li>
                <li> <a href="#services"> <i class="fa-solid fa-code"></i> Servicers</a></li>
                <li><a href="#how_we_work"> <i class="fa-solid fa-briefcase"></i> How we Work</a></li>
                <li><a href="#contact_us"> <i class="fa-solid fa-phone"></i> Contact Us</a></li>
                <li><a href="user/"> <i class="fa-solid fa-user-tie"></i> Login</a></li>
            </ul>
        </nav>
    </header>
    <div class="slideshow-container">
        <?php
            $sql=$con->prepare('SELECT slideimg FROM tblslideshow WHERE slideactive = 1');
            $sql->execute();
            $count = $sql->rowCount();
            $result=$sql->fetchAll();
            $i=1;
            foreach($result as $slide){
                echo '
                    <div class="mySlides">
                        <img src="images/slideshow/'.$slide['slideimg'].'" alt="Image '.$i.'">
                    </div>
                ';
                $i++;
            }
        ?>
        <a class="prev" onclick="prevSlides(1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
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
            <div class="card_work card1">
                <h1>1</h1>
                <p> <span style="font-weight: bold;">Consultation:</span> At the start of every project, we hold an initial consultation with you to understand your needs, goals, and preferences. This conversation allows us to get a clear understanding of what you want to achieve and what your requirements are.</p>
            </div>
            <div class="card_work card2">
                <h1>2</h1>
                <p> <span style="font-weight: bold;">Proposal:</span> Based on the information gathered during the consultation, we create a detailed proposal that outlines the scope of work, timeline, and cost. This proposal gives you a clear idea of what we will deliver and how long it will take.</p>
            </div>
            <div class="card_work card3">
                <h1>3</h1>
                <p> <span style="font-weight: bold;">Agreement:</span> Once you approve the proposal, we finalize the agreement and set up the project. We also ensure that we have all the necessary resources, including personnel, tools, and equipment, to execute the project successfully.</p>
            </div>
            <div class="card_work card4">
                <h1>4</h1>
                <p> <span style="font-weight: bold;">Design and Development:</span> We start working on the project, designing and developing the solution according to the agreed-upon specifications. We work closely with you throughout this phase to ensure that we are meeting your expectations.</p>
            </div>
            <div class="card_work card5">
                <h1>5</h1>
                <p> <span style="font-weight: bold;">Testing and Delivery:</span> Once the solution is ready, we thoroughly test it to ensure it meets your requirements. We fix any bugs or issues identified during testing and deliver the final product to you on time.</p>
            </div>
            <div class="card_work card6">
                <h1>6</h1>
                <p> <span style="font-weight: bold;">Support and Maintenance:</span> We provide ongoing support and maintenance to ensure your solution continues to function as expected. We are always available to address any issues or concerns that may arise.</p>
            </div>
        </div>
        <p>Overall, our goal is to ensure that you are satisfied with the final product and that it meets your needs and goals. We achieve this by prioritizing communication and collaboration throughout the entire process.</p>
    </div>
    <div class="mycv">
        <h1>The Person Behind the Screen</h1>
        <div class="dicription">
            <div class="text">
            My name is Yehia Kobeyssy and I am an Austrian with a technical excellence in administrative informatics and technical diplomas in accounting and informatics. I was born in 1987, and my passion for technology started at a young age. As I grew older, I realized that I wanted to pursue a career in technology, and decided to study administrative informatics at university.<br><br>
            Over the past 15 years, I have gained a wealth of experience in software and computer maintenance. I am skilled in creating and managing databases, working with network systems, and controlling servers. These skills have enabled me to provide high-quality IT management services to my clients.<br><br>
            In addition to my expertise in IT management, I am also a skilled web developer. I have extensive experience in both front-end and back-end development, and I have worked on numerous web development projects over the years. I am experienced in using HTML, CSS, JavaScript, and other web development technologies to create responsive and user-friendly websites.<br><br>
            As a web developer, I am committed to delivering high-quality solutions to meet my clients' needs. I take pride in my ability to work closely with clients to understand their requirements and provide them with solutions that exceed their expectations. I am also committed to staying up-to-date with the latest web development trends and technologies, and I am always looking for ways to improve my skills and knowledge.<br><br>
            In addition to web development, I also have experience in desktop development. Over the years, I have developed a range of desktop applications for clients in various industries. These applications have helped my clients improve their productivity and efficiency, and I take great pride in the work that I have done.<br><br>
            As a technology professional, I understand the importance of delivering high-quality solutions that meet my clients' needs. I am committed to providing exceptional service to all of my clients, and I am always willing to go the extra mile to ensure their satisfaction. I believe that communication is key to a successful project, and I always keep my clients informed throughout the development process.<br><br>
            Overall, I am a highly skilled and experienced technology professional with a strong background in IT management, web development, and desktop development. Whether you need help with IT management, web development, or desktop development, I have the expertise and experience to deliver exceptional results. If you are looking for a reliable and skilled technology professional, look no further than yehia Kobeyssy.<br><br>
            </div>
            <div class="img">
                <img src="images/synpoles/yehia_index.png" alt="">
            </div>
        </div>
    </div>
    <div class="my_portfolio">
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
    <div class="ourteam">
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
                        <h3>'.$per['workerName'].'</h3>
                        <p>'.$per['workerDiscription'].'</p>
                        <a href="mailto:'.$per['Workeremail'].'" class="btn btn-secondary">contact Him/her</a>
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
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h3>About Us</h3>
                    <p>We are a web development company dedicated to providing high-quality services to our clients.</p>
                </div>
                <div class="col-md-3">
                    <h3>Contact Us</h3>
                    <p>Email: info@yktechnology.net</p>
                    <p>Phone: +34 613194204</p>
                </div>
                <div class="col-md-3 follow">
                    <h3>Follow Us</h3>
                    <a href="https://www.facebook.com/yehia.kobeyssy.7">Facebook</a>
                    <a href="https://www.linkedin.com/in/yehia-kobeyssy-818763243/">LinkedIn</a>
                    <a href="https://github.com/Yehiakobeyssy">GitHup</a>
                    <a href="terms.php">Policy and Terms of Service</a>
                </div>
                <div class="col-md-3">
                    <h3>Languge:</h3>
                    <span id="google_element"></span>
                </div>
            </div>
        </div>
    </footer>
    <?php include 'common/jslinks.php'?>
    <script src="index.js"></script>
    <script>
        let slideIndex = 1; 

        function showSlides(n) {
            let i;
            const slides = document.querySelectorAll('.mySlides');
            if (n > slides.length) {
                slideIndex = 1; 
            }
            if (n < 1) {
                slideIndex = slides.length; 
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = 'none';
            }
            slides[slideIndex - 1].style.display = 'block';
        }

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function prevSlides() {
            plusSlides(-1); 
        }

        showSlides(slideIndex);
        setInterval(function() {
            plusSlides(1); 
        }, 6000);
    </script>
</body>