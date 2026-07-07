<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <style>
        input {
            width:200px;
            height:30px;
        }
        select {
            width: 100%;
            height: 100%;
        }
    </style>
    </head>
    <body>
        <h1>Time Sheet</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../timesheet.php">Clock In/Out</a></span>
                <span ><a class="button" href="view_page.php">View Timesheet</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="view_search.php" method="post">
            <h3>Clock In / Clock Out</h3>
            <br><br>

            <TABLE class='form' BORDER="1">
                <?php

                    //Query Successful
                    //Show form for searching timesheet
                    echo "<tr>";
                        echo "<td><b>Username</b></td>";
                        echo "<td align=\"center\"><input type=\"text\" name='username'></td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td><b>Password</b></td>";
                        echo "<td align=\"center\"><input type=\"password\" name='password'></td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td><b>Month</b></td>";
                        echo "<td align=\"center\">";
                        echo "<select name='month'>";
                            echo "<option value=1>January</option>";
                            echo "<option value=2>February</option>";
                            echo "<option value=3>March</option>";
                            echo "<option value=4>April</option>";
                            echo "<option value=5>May</option>";
                            echo "<option value=6>June</option>";
                            echo "<option value=7>July</option>";
                            echo "<option value=8>August</option>";
                            echo "<option value=9>September</option>";
                            echo "<option value=10>October</option>";
                            echo "<option value=11>November</option>";
                            echo "<option value=12>December</option>";
                        echo "</select>";
                        echo "</td>";
                    echo "</tr>";
                    echo "<tr>";
                        $days = date("t");
                        $days = 0+$days;
                        echo "<td><b>Day</b></td>";
                        echo "<td><select name='day'>";
                        echo "<option></option>";
                        for($i = 1; $i <= $days; $i++){
                            echo "<option>$i</option>";
                        }
                        echo "</select></td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td><b>Year</b></td>";
                        echo "<td align=\"center\">";
                        echo "<select name='year'>";
                        $year = date('Y');
                        $year = 0+$year;
                        for($i = $year; $i >= $year - 5; $i--)
                            echo "<option>$i</option>";
                        echo "</select>";
                        echo "</td>";
                    echo "</tr>";
                ?>
            </TABLE><br>
            <td><input type='submit' name='' value='Search Time Sheet'></td>
        </form>
    </body>
</html>
