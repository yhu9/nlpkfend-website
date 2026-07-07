<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <meta http-equiv="refresh" content="100;url=all.php" />
    <style>
        input[type=text] {
            width:200px;
            height:30px;
            display: inline;
        }

    </style>
    </head>
    <body>
    <?php
        $datestr = date('m/d/Y');
        $curdate = new DateTime($datestr);
        echo "<h1>All Students ".$datestr."</h1>";
    ?>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance_home.php">Attendance Home</a></span>
                <span ><a class="button" href="purple.php">Purple Rooms</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <br>

        <?php
            include("../../config.php");
            include("queries.php");
            $db = connect();
            checkSession();

            //get show the data
            showAttendanceAll($db);

        ?>
    </body>
</html>
