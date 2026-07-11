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

        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();

            //get field values for schedule to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Schedule";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();

                //Initialize and create the add schedule sql statement
                $sql1 = "INSERT INTO Schedule (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "scheduleID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = "";
                        if($field->name == 'days_of_week'){
                            $val = '';
                            $sun = $_POST['sun'];
                            $mon = $_POST['mon'];
                            $tue = $_POST['tue'];
                            $wed = $_POST['wed'];
                            $thu = $_POST['thu'];
                            $fri = $_POST['fri'];
                            $sat = $_POST['sat'];

                            $week = array($sun,$mon,$tue,$wed,$thu,$fri,$sat);
                            $count = 0;
                            foreach($week as $d){

                                if($d == 'y' and strlen($val) > 0)
                                    $val .= ',';

                                if($count == 0 and $d == 'y')
                                    $val .= 'sun';
                                elseif($count == 1 and $d == 'y')
                                    $val .= 'mon';
                                elseif($count == 2 and $d == 'y')
                                    $val .= 'tue';
                                elseif($count == 3 and $d == 'y')
                                    $val .= 'wed';
                                elseif($count == 4 and $d == 'y')
                                    $val .= 'thu';
                                elseif($count == 5 and $d == 'y')
                                    $val .= 'fri';
                                elseif($count == 6 and $d == 'y')
                                    $val .= 'sat';


                                $count += 1;
                            }
                        }elseif(strpos($field->name,'phone') !== false){
                            $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                            if($tmp == "--" or $tmp == '-' or $tmp == '')
                                $val = '';
                            else
                                $val = $tmp;
                        }else
                            $val = mysqli_real_escape_string($db,$_POST[$field->name]);

                        if($val != "" and $val != "--"){
                            if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                                $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                                $field->type == 246){
                                if($is_first == 1){
                                    $sql1 .= "$str_fieldname";
                                    $sql2 .= "$val";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",$val";
                                }
                            }else{
                                if($is_first == 1){
                                    $sql1 .= "$str_fieldname";
                                    $sql2 .= "\"$val\"";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",\"$val\"";
                                }
                            } 
                        }
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new schedule
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new schedule!</h3>";

                    //get last inserted schedule
                    $scheduleData = getLastInsert($db,"Schedule");

                    //show schedule data inserted
                    showData($scheduleData['data'],$scheduleData['fields']);

                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new schedule: <b>" .mysqli_error($db). "</b>");
                }
            }else{
                echo("sql statement: ".$sql);
                echo "<br>";
                echo("Could not access database fields: <b>" .mysqli_error($db). "</b>");
            }
            
            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    

    </body>
</html>
