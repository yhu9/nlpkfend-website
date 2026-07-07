<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Add Employee Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../employee.php">Employee Info</a></span>
                <span ><a class="button" href="../add/addEmployee_page.php">Add Employee</a></span>
                <span ><a class="button" href="../search/searchEmployee_page.php">Search Employee</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="executeAddEmployee.php" method="post">
            <h3>Fill Out Your Employee to Add</h3>
            <p>
            <u>NOTE!</u><br>
            1. If empty field, NULL value will be placed<br>
            3. REQUIRED forms must be filled out <br>
            </p> 
            <br><br>
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkAdvancedSession(3);

                    //create query
                    $sql = "SELECT * FROM Employee";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        $data = array();
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        //show the add Employee Form
                        echo "<form action=\"executeAddEmployee.php\" method=\"post\">";
                        showAddForm($data,$fields);
                        echo "<input type=\"submit\" action=\"executeAddEmployee.php\" value=\"Add Employee Now\">\n";
                        echo "</form>\n";

                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>

    </body>
</html>
