<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
<script type='text/javascript'>
function valueChanger(id1,id2){
    document.getElementById(id1).value = document.getElementById(id2).value;
}
    </script>
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

        <br><br>

    <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //get field values for schedule table
        $sql = "SELECT * FROM Schedule";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            //get pid from post
            $pid = $_POST['id'];

            $sql1 = "SELECT * FROM Schedule";
            $sql2 = "WHERE scheduleID = $pid";
            $sql = "$sql1 $sql2";

            $result = mysqli_query($db,$sql);
            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $finfo = $result->fetch_fields();
                echo "<form action=\"execute_updateSchedule.php\" method=\"POST\">\n";
                $found = mysqli_num_rows($result);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }

                //save post information
                $tmp = 0;
                foreach($data as $row){
                    $val = $row["scheduleID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showEditableData2($data,$finfo);
                echo "<input type='submit' value='Update Values'>\n";
                echo "</form>\n";
            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>

</body>
</html> 

