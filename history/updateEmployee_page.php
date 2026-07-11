<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Employee Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../employee.php">Employee Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addEmployee_page.php">Add Employee</a></span>
                <span ><a class="button" href="../delete/deleteEmployee_page.php">Delete Employee</a></span>
                <span ><a class="button" href="../update/updateEmployee_page.php">Edit Employee</a></span>
                <span ><a class="button" href="../search/searchEmployee_page.php">Search Employee</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <h3>Search for the Employees to update</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        </p>
        <br><br>

        <form action="search_updateEmployee.php" method="post" enctype='multipart/form-data'>
                <select class="selectpicker" name="orderby">
                    <option value="" selected>Order By</option>
                    <option>employeeID</option>
                    <option>first_name</option>
                    <option>last_name</option>
                    <option>DOB</option>
                </select><br>
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkAdvancedSession(3);

                    //create query
                    $sql = "SELECT * FROM Employee";
                    $result = mysqli_query($db,$sql);
                    $data = array();

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        showSearchForm($data,$fields);                       

                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $db->close();
                ?>
            <br>

            <input type="submit" action="search_updateEmployee.php" value="Search for Employees to Update">
        </form>
    </body>
</html>
