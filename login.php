<!DOCTYPE HTML>
<?php

    include("config.php");
    $db = connect();
    session_start();

    //Creates session for successful login attempt.
    //TO DO:
    //DELETE login session if the same ipaddress already exists
    function create_session($link, $user,$login_attempt,$ipaddress){
        if($login_attempt == 1) {
            $sql_sess_creation = "REPLACE INTO session (username, login_time, ipaddress) VALUES (  '$user', now(), '$ipaddress')";

            if(!mysqli_query($link,$sql_sess_creation)){
                echo "ERROR: could not execute insert. " . mysqli_error($link);
            }
            else {
                header("location: homepage.php");
            }

        }else{
            $error = "Your Login Name or Password is invalid";
        }
    }

    //Validates login attempt by checking database for username and password
    //TO DO:
    //1. create separate database for admin and session tables
    //2. connect to NLPKDB for querying using the database credentials instead of the 
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      
      $dbuser = mysqli_real_escape_string($db,$_POST['username']);
      $dbpass = mysqli_real_escape_string($db,$_POST['password']); 
      
      $sql = "SELECT id FROM admin WHERE username = '$dbuser' and password = '$dbpass'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $active = $row['active'];
      
      $count = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
      
      // If result matched $myusername and $mypassword in the admin table, table row must be 1 row
      $ip = getUserIP();
      create_session($db,$dbuser,$count,$ip);

      mysqli_close($db);
   }
?>

<html>
   <head>
       <link rel="stylesheet" type="text/css" href="css/homepage.css">
        <link rel="stylesheet" media="screen and (min-device-width: 1281px) and (max-width:3000px)" href="mystyle.css" />
        <link rel="stylesheet" media="screen and (min-device-width: 100px) and (max-width:1280px)" href="mystyle_small.css"/>
        <meta name="viewport" conent="width=device-width, initial-scale=1.0">
      <title>Login Page</title>
      <style type = "text/css">
         body {
            font-family:Arial, Helvetica, sans-serif;
            font-size:14px;margin-top: 200px;
         }
         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }
         
        form{
            background-color: #FFFFFF;
        }

        input{
            width: 70%;
            height: 30px;
        }

      </style>
   </head>
   <body bgcolor = "#FFFFFF" >
    <p color="#ffffff">
    <h1 align="center"><u> NLPS & CC Database </u></h1>
      <div align = "center">
         <div style = "width:300px; border: solid 3px #333333; background-color: #ffffff; " align = "left">
            <div style = "background-color:#333333; color:#FFFFFF; padding:5px; "><b>Login</b></div>
            <div >
               <form action = "" method = "post" >
                    <br>
                  <label>UserName  :  </label><input type = "text" class='tall' name = "username" /><br /><br />
                  <label>Password  :  </label><input type = "password" class="tall" name = "password" /><hr>
               <div style = "font-size:11px; color:#ffffff; margin-top:10px"><?php echo $error; ?></div>
                  <input class='rectpretty' type = "submit" value = " Submit "/><br />
               </form>
            </div>
         </div>
      </div>
    </p>
        <!-- Footer -->
		<footer class="footer-distributed">

			<div class="footer-left">

				<h3><span>Northern Lights Preschool</span> & Childcare</h3>

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
