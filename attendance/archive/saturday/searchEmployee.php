<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Search Employee Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance_home.php">Attendance Home</a></span>
                <span ><a class="button" href="saturday.php">Saturday room</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("queries.php");
            include("../../config.php");
            $db = connect();
            checkSession();

            //get field values for Attendance to add
            //names from POST are Table column names
            $first_name = mysqli_real_escape_string($db,$_POST['first_name']);
            $last_name = mysqli_real_escape_string($db,$_POST['last_name']);
            $id = mysqli_real_escape_string($db,$_POST['id']);

            //get the employee data
            if($id != '')
                $employeeData = getemployeeByID($db,$id);
            elseif($first_name != '' or $last_name != ''){
                $employeeData = getemployeeData($db,$first_name,$last_name);
                $id = $employeeData['data'][0]['employeeID'];
            }

            //show the employee data
            $found = count($employeeData['data'] ?? []);
            echo "<h2 align='center'><u>employee for which attendance is added</u></h2>\n";
            showData($employeeData['data'],$employeeData['fields']);
            $tmp = 0;

            //when initial query to find employees is not found
            $db->close();
            echo "<form action='executeEmployee.php' method='post'>\n";
            echo "<br><br>\n";

            //save id to post
            echo "<input type='hidden' name='id' value=$id>\n";
            echo "<TABLE class='form' BORDER=\"1\">\n";

                echo "<tr>\n";
                    echo "<td><b>initial: </b></td>\n";
                    echo "<td align=\"center\"><input style='height:50px;' type=\"text\" name='name'></td>\n";
                echo "</tr>\n";
        ?>
            </TABLE><br>
            <input type='submit' name='type' value='sign in'>
            <input type='submit' name='type' value='sign out'>
        </form>
        
    </body>
</html>
