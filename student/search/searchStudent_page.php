<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Student Student Page</h1>
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
        <h3>Search for the student or students</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        <br><br>
        </p>

        <form action="searchStudent.php" method="post">
            <select class="selectpicker" name="orderby">
                <option value="" selected>Order By</option>
                <option>first_name</option>
                <option>last_name</option>
                <option>room</option>
            </select><br>

            <TABLE class='form' BORDER="1">
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
            </TABLE><br>
            <input type="submit" action="searchStudent.php" value="Search Students">
        </form>
    </body>
</html>
