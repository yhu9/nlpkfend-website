<?php
    //Gate this admin landing page behind an active session (issue #12).
    //Placed above all output so checkSession()'s redirect header is valid.
    include("config.php");
    $db = connect();
    if(checkSession() !== 1){
        //checkSession() has already queued the login redirect; stop here so
        //the admin navigation is never rendered for an unauthenticated client.
        exit;
    }
?>
<!DOCTYPE html>
<html>

   <head>
       <link rel="stylesheet" type="text/css" href="css/homepage.css">
        <link rel="stylesheet" media="screen and (min-device-width: 1281px) and (max-width:3000px)" href="mystyle.css" />
        <!--link rel="stylesheet" media="screen and (min-device-width: 100px) and (max-width:1280px)" href="mystyle_small.css" -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>NLPS & Childcare Database</title>
    <style>

    </style>

   </head>
   <body>

       <h1><u>Northern Lights Preschool Database</u></h1>
        <br><br>
        <hr>
        <br><br>

       <h2><u>Database Tools</u></h2>

        <!-- Center Navigation Page -->
        <table class="homepage" align='center'>
        <tr><td>
       <table class="homepage" align="center">
            <!-- Employee Column -->
            <tr><th>Employee</th></tr>
           <tr>
               <td><form action="employee/employee.php"><input type="submit" value="Employee Info"></form></td>
           </tr>
           <tr>
               <td><form action="scheduler/schedule.php"><input type="submit" value="Employee Schedules"></form></td>
           </tr>
           <tr>
            <td></td>
           </tr>
           <tr>
            <td></td>
           </tr>
       </table>
        </td>
        <td>
       <table class="homepage" align="center">
            <!-- Student Column -->
            <tr><th>Student</th></tr>
           <tr>
               <td><form action="student/student.php"><input type="submit" value="Student Info"></form></td>
           </tr>

           <tr>
               <td><form action="account/account.php"><input type="submit" value="Student Account Info"></form></td>
           </tr>
           <!-- Sign In/Out attendance prototype archived (GH #7): the attendance/
                sub-app is unfinished (room buttons pointed at nonexistent pages),
                so it is no longer linked from the live homepage. Files remain under
                attendance/ (room pages under attendance/archive/) for future work. -->
            <tr><td></td></tr>
       </table>
        </td>
        <td>
       <table class="homepage" align="center">
            <!-- Admin Column -->
            <tr><th>Administration</th></tr>
            <!-- WAITING FOR APPROVAL
           <tr>
               <td><form action="admin_attendance/attendance.php"><input type="submit" value="Advanced Attendance Control"></form></td>
           </tr>
            -->
           <tr>
               <td><form action="expenditure/expenditure.php"><input type="submit" value="Expenditures"></form></td>
           </tr>
           <tr>
               <td><form action="income/income.php"><input type="submit" value="Income"></form></td>
           </tr>
           <tr>
               <td><form action="receipt/receipt.php"><input type="submit" value="Receipts"></form></td>
           </tr>
           <tr>
               <td><form action="log/log.php"><input type="submit" value="Log Book"></form></td>
           </tr>
       </table>
        </td></tr>
        </table>
        <br><br><br><br>
        <a href="logout.php"><u>Logout</u></a>
        <!-- end body -->
        
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
