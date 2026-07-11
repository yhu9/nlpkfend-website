<!DOCTYPE html>
<html>
   
   <head>
       <link rel="stylesheet" type="text/css" href="../../css/homepage.css">
        <link rel="stylesheet" media="screen and (min-device-width: 1281px) and (max-width:2000px)" href="../../mystyle.css" />
        <link rel="stylesheet" media="screen and (min-device-width: 100px) and (max-width:1280px)" href="../../mystyle_small.css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>NLPS & Childcare Database</title>
    <style>
        select {
            width: 100%;
            height: 30px;
        }

    </style>

   </head>
   <body>
        <!-- Header -->

   <h1><u>Northern Lights Preschool Database</u></h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance.php">Attendance Table</a></span>
                <span ><a class="button" href="../delete/deleteAttendance_page.php">Delete Attendance</a></span>
                <span ><a class="button" href="../update/updateAttendance_page.php">Edit Attendance</a></span>
                <span ><a class="button" href="../view/view_navigation.php">View Forms</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <br><br>
        <br><br>
       <h2 align="center"> Please Insert the Month and Year Then choose the form you wish to see</h2>

        <!-- Center Navigation Page -->
        <form method="post">
        <table class='form' align='center' border='1'>
        <tr>
            <th><b>Month</b></th>
            <td align=\"center\">
            <select name='month'>
                <option value=1>January</option>
                <option value=2>February</option>
                <option value=3>March</option>
                <option value=4>April</option>
                <option value=5>May</option>
                <option value=6>June</option>
                <option value=7>July</option>
                <option value=8>August</option>
                <option value=9>September</option>
                <option value=10>October</option>
                <option value=11>November</option>
                <option value=12>December</option>
            </select>
            </td>
        </tr>
        <?php
        echo "<tr>\n";
            $year = date("Y");
            echo "<th><b>Year</b></th>\n";
            echo "<td><select name='year'>\n";
            echo "<option>$year</option>\n";
            for($i = $year - 1; $i >= $year-20; $i--){
                echo "<option>$i</option>\n";
            }
            echo "</select></td>\n";
        echo "</tr>\n";
        ?>
        </table>
        <br><br>
        <table class="homepage" align='center'>
        <tr><td>
       <table class="homepage" align="center">
            <tr><th></th></tr>
           <tr>
               <td><input type="submit" value=""></td>
           </tr>
           <tr>
               <td><input formaction='execute_cover.php' type="submit" value="Cover Page"></td>
           </tr>
           <tr>
               <td><input type="submit" value=""></td>
           </tr>
       </table>
        </td>
        <td>
       <table class="homepage" align="center">
            <tr><th></th></tr>
           <tr>
               <td><input type="submit" value=""></td>
           </tr>
           <tr>
               <td><input formaction='execute_full.php' type="submit" value="Full Attendance Sheet"></td>
           </tr>
           <tr>
               <td><input type="submit" value=""></td>
           </tr>
       </table>
        </td>
        <td>
       <table class="homepage" align="center">
            <tr><th></th></tr>
           <tr>
               <td><input type="submit" value=""></td>
           </tr>
           <tr>
               <td><input formaction='edit_attendance.php' type="submit" value="Edit Attendance Sheet"></td>
           </tr>
           <tr>
               <td><input type="submit" value=""></td>
           </tr>
       </table>
        </td></tr>
        </table>
        </form>
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
