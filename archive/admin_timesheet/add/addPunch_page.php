<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Punch Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                <span ><a class="button" href="../update/updatePunch_page.php">Update Punch</a></span>
                <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
            <h3>Fill Out Your Punch to Add</h3>
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

                    //create query
                    $sql = "SELECT fk_employeeID,date,time,type FROM Punch";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        $data = array();
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        //show the add Punch Form
                        echo "<form action=\"executeAddPunch.php\" method=\"post\">";
                        showAddForm($data,$fields);
                        echo "<input type=\"submit\" action=\"executeAddPunch.php\" value=\"Add Punch Now\">\n";
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
