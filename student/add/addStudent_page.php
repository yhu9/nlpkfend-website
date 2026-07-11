<html>
    <head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    </head>
    <body>
        <h1>Add Student Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../student.php">Student Info</a></span>
                <span ><a class="button" href="../cca/cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="../add/addStudent_page.php">Add Student</a></span>
                <span ><a class="button" href="../search/searchStudent_page.php">Search Student</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
            <h3>Fill Out Your Student to Add</h3>
            <p>
            <u>NOTE!</u><br>
            1. If empty field, NULL value will be placed<br>
            3. REQUIRED forms must be filled out <br>
            </p> 
            <br><br>

            <?php

                    //connect to database
                    include("/var/www/html/config.php");
                    $db = connect();
                    checkAdvancedSession(3);


                    //create query
                    $sql = "SELECT * FROM Student";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        $data = array();
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        //show the add Student Form
                        echo "<form action=\"executeAddStudent.php\" method=\"post\">";
                        showAddForm2($db,$data,$fields);
                        echo "<input type=\"submit\" action=\"executeAddStudent.php\" value=\"Add Student Now\">\n";
                        echo "</form>\n";

                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $db->close();
                ?>
    </body>
</html>
