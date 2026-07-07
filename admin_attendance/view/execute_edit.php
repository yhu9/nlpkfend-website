<!DOCTYPE html>
<html>
    <meta http-equiv='refresh' content='1;url=view_navigation.php'/>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Attendance Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance.php">Attendance Table</a></span>
                <span ><a class="button" href="../delete/deleteAttendance_page.php">Delete Attendance</a></span>
                <span ><a class="button" href="../update/updateAttendance_page.php">Edit Attendance</a></span>
                <span ><a class="button" href="../view/view_navigation.php">View Attendance</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkSession();

        //initialize variables
        $count = (int)$_POST['count'];
        $attendanceIDs = array();

        //create the sql statements to execute and execute according to the previous value
        for($i = 0; $i < $count; $i++){
            //get post information
            $postname = $_POST["row$i"];
            $postval = $_POST["$postname"];

            //check if a form was changed
            if($postval != ''){
                $val = explode(',',$postname);
                $datestr = $val[0];
                $sid = $val[1];
                $prev = $val[2];

                //update if attendances already exist
                if($prev == 'F' or $prev == 'P' and $postval != 'X'){
                    $sql1 = "UPDATE Attendance";
                    $sql2 = "SET tuition_type = '$postval'";
                    $sql3 = "WHERE date = '$datestr' AND fk_studentID = $sid";

                    $sql = "$sql1 $sql2 $sql3";
                    $result = mysqli_query($db,$sql);
                    if($results !== false){
                        echo "<h1>Successfully Changed Attendance!</h1><br>\n";
                    }else
                        echo "<h1>Error Updating Monthly Attendance</h1>";
                    
                //update if attendances already exist and post value is X
                }elseif($prev == 'F' or $prev == 'P' and $postval == 'X'){
                    $sql1 = "DELETE Attendance";
                    $sql2 = "WHERE date = '$datestr' AND fk_studentID = $sid";

                    $sql = "$sql1 $sql2 $sql3";
                    $result = mysqli_query($db,$sql);
                    if($results !== false){
                        echo "<h1>Successfully Changed Attendance!</h1><br>\n";
                    }else
                        echo "<h1>Error Updating Monthly Attendance</h1>";

                //insert if attendance does not exist. However, data is not accurate and must be edited
                }elseif($prev == 'X'){
                    $studentData = getStudentByID($db,$sid);
                    $room = $studentData['data'][0]['room'];
                    $tuition_type = $studentData['data'][0]['tuition_type'];
                    $auth_type = $studentData['data'][0]['auth_type'];
                    if($postval == 'F')
                        $postval = "Full";
                    else
                        $postval = "Part";

                    $sql1 = "INSERT INTO Attendance";
                    $sql2 = "(date,time,type,verifier,room_name,tuition_type,auth_type,fk_studentID)";
                    $sql3 = "VALUES ('$datestr','12:00:00','sign in','admin','$room','$postval','$auth_type',$sid)";

                    $sql = "$sql1 $sql2 $sql3";
                    $result = mysqli_query($db,$sql);
                    if($result !== false){
                        echo "<h1>Successfully Changed Attendance!</h1>";
                    }else{
                        echo "<h1>Error Updating Monthly Attendance</h1>";
                    }
                }

                //update student if authorization type was changed
                elseif($prev == 'O' OR $prev == 'C' OR $prev == 'S'){
                    $sql1 = "UPDATE Student";
                    $sql2 = "SET auth_type = '$postval'";
                    $sql3 = "WHERE studentID = $sid";

                    $sql = "$sql1 $sql2 $sql3";
                    $result = mysqli_query($db,$sql);
                    if($results !== false){
                        echo "<h1>Successfully Changed Attendance!</h1><br>\n";
                    }else
                        echo "<h1>Error Updating Monthly Attendance</h1>";
                }
            }
        }
        $db->close();

        ?>
</body>
</html> 
