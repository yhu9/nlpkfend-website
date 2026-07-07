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
                <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                <span ><a class="button" href="../update/updatePunch_page.php">Update Punch</a></span>
                <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
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

        //initialize variables
        $count = (int)$_POST['count'];
        $punchIDs = array();
        $statements = array();
        
        //Initialize sql statements
        $sql1 = "UPDATE Punch";
        $sql2 = "SET ";
        $sql3 = "WHERE";

        //get Punch Fields
        $sql = "SELECT * FROM Punch";
        $result = mysqli_query($db,$sql);
        $finfo = $result->fetch_fields();

        //create the sql statements to execute
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            
            $firstpass = 1;
            $change = false;
            foreach($finfo as $field){
                $str_fieldname = mysqli_real_escape_string($db,$field->name);
                $fieldname = "$str_fieldname$id";
                $val = "";
                if($field->name == "DOB" or strpos($field->name,'date') !== false){
                    $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$fieldname]));
                    if($tmp != '--'){
                        $date = DateTime::createFromFormat("m-d-Y",$tmp);
                        $val = $date->format('Y-m-d');
                    }else
                        $val = "";
                }elseif($field->name == 'time'){
                    $fieldname = "$field->name$id";
                    $str_time = implode(':',$_POST[$fieldname]);
                    if($str_time == ":"){
                        $val = "";
                    }else{
                        $extension = "time_ext$id";
                        $ext = mysqli_real_escape_string($db,$_POST[$extension]);
                        $str_time = "$str_time $ext";
                        $val = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                    }
                }
                else
                    $val = mysqli_real_escape_string($db,$_POST[$fieldname]);

                if($val != "" and $val != ":" and $val != "--"){
                    //if values were found set flags
                    $change = true;

                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name = $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name = '$val'";
                    }

                    if($firstpass ==1){
                        $sql2 .= "$condition";
                        $firstpass = 0;
                    }else{
                        $sql2 .= ",$condition";
                    }
                }
            }
            //create sql3
            $sql3 = "WHERE punchID = $id";

            //combine and push combined sql to statement according to change flag for the employee
            if($change){
                array_push($empIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $PunchIDs[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false and $id != ""){
                echo "successfully updated punch!<br>\n";
                $punchData = getPunchByID($db,$id);
                showData($punchData['data'],$punchData['fields']);
            }else{
                echo "Error with sql statement: $sql <br>\n";
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
