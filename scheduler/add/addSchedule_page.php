<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Schedule Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../schedule.php">Schedule Table</a></span>
                <span ><a class="button" href="../add/addSchedule_page.php">Add Schedule</a></span>
                <span ><a class="button" href="../search/searchSchedule_page.php">Search Schedule</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
            <h3>Fill Out Your Schedule to Add</h3>
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
                    checkSession();

                    $id = $_POST['id'];
                    if($id != ''){
                        $e_data = getEmployeeByID($db,$id);
                        showData($e_data['data'],$e_data['fields']);
                        echo "<br><br>";
                    }


                    //create query
                    $sql = "SELECT * FROM Schedule";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        $data = array();
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        //show the add Schedule Form
                        echo "<form action=\"executeAddSchedule.php\" method=\"post\">";
                        showAddForm2($db,$data,$fields);
                        echo "<input type=\"submit\" action=\"executeAddSchedule.php\" value=\"Add Schedule Now\">\n";
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
