<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Student Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../student.php">Student Table</a></span>
                <span ><a class="button" href="../add/addStudent_page.php">Add Student</a></span>
                <span ><a class="button" href="../delete/deleteStudent_page.php">Delete Student</a></span>
                <span ><a class="button" href="../update/updateStudent_page.php">Edit Student</a></span>
                <span ><a class="button" href="../search/searchStudent_page.php">Search Student</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="search_deleteStudent.php" method="post">
            <h3>Search for the student or students to delete</h3>
            <u>NOTE!</u><br>
            1. Empty fields will not be used<br>
            <br><br>
            <form action="searchStudent.php" method="post">
                <select class="selectpicker" name="orderby">
                    <option value="" selected>Order By</option>
                    <option>studentID</option>
                    <option>first_name</option>
                    <option>last_name</option>
                    <option>start_date</option>
                </select><br>

                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT * FROM Student";
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

                    $result->free();
                    $db->close();
                ?>
            <input type="submit" action="search_deleteStudent.php" value="Search for students to delete">
        </form>
    </body>
</html>
