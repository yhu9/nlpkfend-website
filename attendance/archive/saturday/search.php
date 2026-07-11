<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Search Student Page</h1>
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

            //get the student data
            if($id != '')
                $studentData = getStudentByID($db,$id);
            elseif($first_name != '' or $last_name != ''){
                $studentData = getStudentData($db,$first_name,$last_name);
                $id = $studentData['data'][0]['studentID'];
            }

            //show the student data
            $found = count($studentData['data'] ?? []);
            echo "<h2 align='center'><u>Student for which attendance is added</u></h2>\n";
            showData($studentData['data'],$studentData['fields']);
            $tmp = 0;

            //save id to post

            //when initial query to find students is not found
            $db->close();
            echo "<form action='execute.php' method='post'>\n";
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
            <input class='rectpretty' type='submit' style='height:50px;' name='type' value='sign in'>
            <input class='rectpretty' type='submit' style='height:50px;' name='type' value='sign out'>
        </form>
        
    </body>
</html>
