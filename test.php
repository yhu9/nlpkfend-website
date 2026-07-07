<!DOCTYPE HTML>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    <!--meta http-equiv="refresh" content="100;url=all.php" /-->
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
            </div>
        <hr>
        </div>
        <br>

        <?php
            include("/var/www/html/config.php");
            include("/var/www/html/attendance/all/queries.php");
            $db = connect();
            checkSession();

            //pass the database information to javascript only if session passes
            //if($sess == 1){
                $sdata = queryActiveStudents($db);
                echo "<script type='text/javascript'>\n";
                echo 'var phpdata = ' . json_encode($sdata['data']) .';' ;
                echo "</script>\n";
            //}else{
            //    echo "<p class='error'>THERE IS A PROBLEM WITH YOUR CONNECTION <br> PLEASE LOG OUT AND LOG BACK IN</p>";
            //}

            //get show the data
            showAttendance($db);

        ?>
    </body>
</html>
