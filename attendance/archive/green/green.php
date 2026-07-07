<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <meta http-equiv="refresh" content="100;url=green.php" />
    <style>

    </style>
    </head>
    <body style='text-align:center;'>
    <?php
        $datestr = date('m/d/Y');
        $curdate = new DateTime($datestr);
        echo "<h1>Green Room ".$datestr."</h1>";
    ?>
        <br><br><br>
        <div class="menu_color">
        <hr style='width:100%; padding:0; margin:0; margin-right:0px;'>
            <div style='width:100%; margin-left:0px;' class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance_home.php">Attendance Home</a></span>
                <span ><a class="button" href="green.php">Green room</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            include("../../config.php");
            include("queries.php");
            $db = connect();
            checkSession();

            //get show the data
            showAttendance($db);

?>
    </body>
</html>
