<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Search Log Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../log.php">Log Information</a></span>
                <span ><a class="button" href="../search/searchLog_page.php">Search Log</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <h3>Search for the log or logs</h3>
        <br><br>

        <form action="searchLog.php" method="post">

                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT logID, pk_info, table_name,date,function,users_logged_in FROM Log";
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
            <input type="submit" action="searchLog.php" value="Search Log">
        </form>
    </body>
</html>


