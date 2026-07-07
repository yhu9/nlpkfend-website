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
        <h3>Search for the schedule or schedules</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        <br><br>
        </p>

        <form action="searchSchedule.php" method="post">
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
                    $sql = "SELECT * FROM Schedule";
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
            <input type="submit" action="searchSchedule.php" value="Search Schedules">
        </form>
    </body>
</html>
