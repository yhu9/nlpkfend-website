<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <style>
        input {
            width:200px;
            height:30px;
        }
    </style>
    </head>
    <body>
        <h1>Time Sheet</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="timesheet.php">Clock In/Out</a></span>
                <span ><a class="button" href="view/view_page.php">View Timesheet</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="execute.php" method="post">
            <h3>Clock In / Clock Out</h3>
            <br><br>

            <TABLE class='form' BORDER="1">
                <?php

                    //Query Successful
                    //Show form for adding an emergency_contact
                    echo "<tr>";
                        echo "<td><b>Username</b></td>";
                        echo "<td align=\"center\"><input type=\"text\" name='username'></td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td><b>Password</b></td>";
                        echo "<td align=\"center\"><input type=\"password\" name='password'></td>";
                    echo "</tr>";
                ?>
            </TABLE><br>
            <td><input type='submit' name='type' value='clock in'></td>
            <td><input type='submit' name='type' value='clock out'></td>
        </form>
    </body>
</html>
