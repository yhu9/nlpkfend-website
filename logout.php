<?php

include('config.php');
$conn = connect();

$ip = getUserIp();

$sql = "DELETE FROM session WHERE ipaddress = '$ip'";

if(mysqli_query($conn,$sql)){
    echo "Successfully logged out!";
}

?>

<html>
   
   <head>
       <link rel="stylesheet" type="text/css" href="mystyle.css">
       <link rel="stylesheet" type="text/css" href="css/logout.css">
       <title>NLPS & Childcare Database</title>
    <style>

    </style>

   </head>
   <body>
        <!-- Header -->
        <h1 class='logout' align="center"> Thanks For Logging Out! You're Awesome! </h1>
        <a href="login.php">Back to login</a>

        <!-- Footer -->
		<footer class="footer-distributed">

			<div class="footer-left">

				<h3>Company<span>logo</span></h3>

				<p class="footer-links">
                    <a href="http://www.nlpkak.com">Company Website</a>
                    :
					<a href="http://www.nlpkak.com">nlpkak.com</a>
				</p>

				<p class="footer-company-name">NLPS and Childcare</p>
			</div>

			<div class="footer-center">

				<div>
					<i class="fa fa-map-marker"></i>
					<p><span>703 W Northern Lights Blvd</span> Anchorage, Alaska</p>
				</div>

				<div>
					<i class="fa fa-phone"></i>
					<p>+1 (907) 274-2040</p>
				</div>

				<div>
					<i class="fa fa-envelope"></i>
					<p><a href="mailto:support@company.com">nlp99503@yahoo.com</a></p>
				</div>

			</div>

			<div class="footer-right">

				<p class="footer-company-about">
                    <span>About the company</span>
    
                    Welcome to Northern Lights Preschool & Child Care!
                    <br>

                    We provides fun, warm, exciting, dependable and loving atmosphere where children can enjoy learning and discover their potential.

				</p>

				<div class="footer-icons">
					<a href="#"><i class="fa fa-facebook"></i></a>
					<a href="#"><i class="fa fa-twitter"></i></a>
					<a href="#"><i class="fa fa-linkedin"></i></a>
					<a href="#"><i class="fa fa-github"></i></a>
				</div>
			</div>
		</footer>
   </body>
</html>
