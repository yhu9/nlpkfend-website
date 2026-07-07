<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Student Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <h3>Search for the student or students</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        2. studentID is unique to each individual<br>
        <br><br>
        </p>

        <form action="search_student_balance.php" method="post">
            <select class="selectpicker" name="orderby">
                <option value="" selected>Order By</option>
                <option>studentID</option>
                <option>first_name</option>
                <option>last_name</option>
                <option>start_date</option>
            </select><br>

            <TABLE class='form' BORDER="1">
                <?php
                    //connect to database
                    include("../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT * FROM Student";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for adding an student
                        $finfo = $result->fetch_fields();
                        foreach($finfo as $field){
                            if($field->name == "first_name" or $field->name == "last_name" or $field->name == "studentID"){
                                echo "<tr>";
                                echo "<td><b>$field->name</b></td>";
                                if($field->name == "first_name")
                                    echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\" placeholder='first name of child'></td>";
                                elseif($field->name == "last_name")
                                    echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\" placeholder='last name of child'></td>";
                                elseif($field->name == "studentID")
                                    echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\"></td>";
                                echo "</tr>";
                            }
                        }
                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>
            </TABLE><br>
            <input type="submit" action="searchStudent.php" value="search">
        </form>
    </body>
</html>
