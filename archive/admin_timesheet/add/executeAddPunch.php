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

        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();

            //get field values for punch to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Punch";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();
                //Initialize and create the add punch sql statement
                $sql1 = "INSERT INTO Punch (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "punchID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = "";
                        if($field->name == "DOB" or strpos($field->name,'date') !== false){
                            $tmp = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
                            $date = DateTime::createFromFormat("m-d-Y",$tmp);
                            $val = $date ? $date->format('Y-m-d') : "";
                        }elseif($field->name == 'time'){
                            $str_time = implode(':',(array)($_POST['time'] ?? []));
                            if($str_time == ":"){
                                $val = "";
                            }else{
                                $ext = mysqli_real_escape_string($db,$_POST['time_ext']);
                                $str_time = "$str_time $ext";
                                $val = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                            }
                        }elseif($field->name == "fk_employeeID"){
                            if (isset($result) && $result instanceof mysqli_result) $result->free();
                            $first_name = mysqli_real_escape_string($db,$_POST["first_name"]);
                            $last_name = mysqli_real_escape_string($db,$_POST["last_name"]);
                            $employeeID = 0;
                            $employee_data = getEmployeeSearchData($db,$first_name,$last_name);
                            if(count($employee_data['data'] ?? []) == 1)
                            {
                                echo "<h2>employee for which punch is added<br></h2>";
                                showData($employee_data['data'],$employee_data['fields']);
                                $employeeID = $employee_data['data'][0]['employeeID'];
                            }
                            $val = "".$employeeID;
                        }
                        else
                            $val = mysqli_real_escape_string($db,is_array($_POST[$field->name] ?? '')?'':($_POST[$field->name] ?? ''));

                        if($val != "" and $val != "--" and $val != ":"){
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
                                    $sql2 .= "'$val'";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",'$val'";
                                }
                            } 
                        }
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new punch
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new punch!</h3>";
                    
                    //get last insert data
                    $insert_data = getLastInsertData($db);

                    //show last insert data
                    showData($insert_data["data"],$insert_data["fields"]);

                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new punch: <b>" .mysqli_error($db). "</b>");
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
