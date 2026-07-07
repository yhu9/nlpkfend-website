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

        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        include("../../upload.php");
        $db = connect();

        //initialize variables
        $count = (int)$_POST['count'];
        $scheduleIDs = array();
        $statements = array();

        //get schedule fields
        $tmpsql = "SELECT * FROM Schedule";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();

        //create the sql statements to execute
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Schedule";
            $sql2 = "SET ";
            $sql3 = "WHERE";
           
            //get the id
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);

            //create change flag
            $change = false;

            //check if the row is updated at all and create the body of the sql update statement
            if($result !== false){
                $firstpass = 1;
                foreach($finfo as $field){
                    $val = "";
                    $fieldname = "$field->name$id";
                    $predata = getFieldValue($db,"Schedule",$field->name,$id);
                    $preval = $predata['data'][0][$field->name];
                    if($field->name == "DOB" or strpos($field->name,'date') !== false){
                        $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$fieldname]));
                        if($tmp == '--' or $tmp == '-' or $tmp == ''){
                            $newval = "";
                        }else{
                            $date = DateTime::createFromFormat("m-d-Y",$tmp);
                            $newval = $date->format('Y-m-d');
                        }
                    }elseif(strpos($field->name,'time') !== false or $field->name == 'lunch_in' or $field->name == 'lunch_out'){
                        $str_time = implode(':',$_POST[$fieldname]);
                        if($str_time == ':' or $str_time == '' or $str_time == 'PM' or $str_time == 'AM'){
                            $newval = '';
                        }else{
                            $ext = $_POST["time_ext$field->name$id"];
                            $str_time = "$str_time $ext";
                            $t = date('H:i:s', strtotime($str_time));
                            $newval = $t;
                        }
                    }elseif($field->name == 'days_of_week'){
                        $newval ='';
                        $tmp = array();
                        $tmp[0] = $_POST['sun'];$tmp[3] = $_POST['wed'];
                        $tmp[1] = $_POST['mon'];$tmp[4] = $_POST['thu'];
                        $tmp[2] = $_POST['tue'];$tmp[5] = $_POST['fri'];
                        $tmp[6] = $_POST['sat'];

                        for($i = 0; $i < 7; $i++){
                            if($tmp[$i] == 'y' and $newval != '')
                                $newval .= ',';

                            if($tmp[$i] == 'y' and $i == 0)
                                $newval .= 'sun';
                            elseif($tmp[$i] == 'y' and $i == 1)
                                $newval .= 'mon';
                            elseif($tmp[$i] == 'y' and $i == 2)
                                $newval .= 'tue';
                            elseif($tmp[$i] == 'y' and $i == 3)
                                $newval .= 'wed';
                            elseif($tmp[$i] == 'y' and $i == 4)
                                $newval .= 'thu';
                            elseif($tmp[$i] == 'y' and $i == 5)
                                $newval .= 'fri';
                            elseif($tmp[$i] == 'y' and $i == 6)
                                $newval .= 'sat';
                        }

                    }else
                        $newval = $_POST[$fieldname];

                    //look for a change
                    if(strpos($field->name,'ID') == false AND $preval != $newval){
                        $change = true;
                    }

                    //if change was found in the row
                    if($change){
                        $condition = "";
                        if($firstpass != 1)
                            $condition .= ',';
                        else
                            $firstpass = 0;

                        //check if the val is: NULL,NUMERIC,STRING
                        if($newval == ""){
                            $condition .= "$field->name = NULL";
                        }elseif($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                            $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                            $field->type == 246)
                        { 
                            $condition .= "$field->name = $newval";
                        }else{
                            $condition .= "$field->name = \"$newval\"";
                        }
                        
                        $sql2 .= "$condition";
                    }
                }
            }
            //create sql3
            $sql3 = "WHERE scheduleID = $id";

            //combine and push combined sql to statement
            if($change){
                array_push($scheduleIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $scheduleIDs[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "schedule updated!";
                $scheduleData = getScheduleByID($db,$id);
                showData($scheduleData['data'],$scheduleData['fields']);
            }else{
                echo "Error with sql statement: $sql <br>\n";
                echo "Error Discription: ".mysqli_error($db)."<br>\n";
            }

            $count++;
        }
        if($count == 0){
            echo "<h1>Nothing Changed</h1>\n";
        }

        $result->free();
        $db->close();
        ?>
</body>
</html> 
