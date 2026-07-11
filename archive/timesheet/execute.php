<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <meta http-equiv="refresh" content="10;url=timesheet.php" />
    <body>
        <h1>Time Sheet</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="timesheet.php">Clock In/Out</a></span>
                <span ><a class="button" href="view/view_page.php">View Timesheet</a></span>
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("queries.php");
            include("../config.php");
            $db = connect();
            checkSession();

            //get field values for Punch to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Punch";
            $result = mysqli_query($db,$sql);
            if($result !== false){
                //save punch fields
                $punch_fields = mysqli_fetch_fields($result);

                //Check if one or more student is found using that query
                $username = mysqli_real_escape_string($db,$_POST["username"]);
                $password = mysqli_real_escape_string($db,$_POST["password"]);

                //get the employee data
                $employeeData = getEmployee($db,$username,$password);

                //Show the employee data for which we are adding the punch
                $found = count($employeeData['data'] ?? []);
                echo "<h2 align='center'><u>Employee for which punch is added</u></h2>\n";
                showData($employeeData['data'],$employeeData['fields']);

                //CHECK to see if the number of rows found is only 1 otherwise something is wrong
                if($found == 1){
                    //save the employee ID for later use
                    $employeeID = $employeeData['data'][0]['employeeID'];

                    //Initialize and create the insert Punch sql statement
                    $sql1 = "INSERT INTO Punch(";
                    $sql2 = "VALUES (";
                    $is_first = true;
                    foreach($punch_fields as $field){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        if($field->name != "punchID"){

                            if($field->name == 'fk_payrollID'){
                                $val = 'NULL';
                            }elseif($field->name == 'time'){
                                $val = 'TIME(NOW())';
                            }elseif($field->name == 'date'){
                                $val = 'DATE(NOW())';
                            }elseif($field->name == 'type'){
                                $val = $_POST['type'];
                            }elseif($field->name == 'fk_employeeID'){
                                $val = $employeeID;
                            }

                            if($val != ""){
                                if($is_first){
                                    $sql1 .= "$str_fieldname";
                                    $sql2 .= "$val";
                                    $is_first= 0;
                                }elseif($field->type < 14){
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",$val";
                                }else{
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",'$val'";
                                }
                            }
                        }
                    }
                    $sql1 .= ")";
                    $sql2 .= ")";

                    //Create the combined sql statement and execute the addition of the new emergency_contact
                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $sql = "$sql1 $sql2";
                    $result = mysqli_query($db,$sql);

                    //Check to make sure the INSERT statement executed
                    if($result !== false){
                        echo "<h3 align=\"center\">Successfully added new Punch!</h3>";
                        
                        //get last insert data
                        $insert_data = getLastInsertData($db);

                        //show last insert data
                        showData($insert_data["data"],$insert_data["fields"]);

                    }else{
                        echo("sql statement: " .$sql);
                        echo "<br>";
                        echo("Could not add the new Punch: <b>" .mysqli_error($db). "</b>");
                    }
                //when there is more than one student or no student found
                }else{
                    echo "<h2 align='center'>Number of rows found is WRONG. MUST BE ONE</h2><br>\n";
                }
            }
            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    </body>
</html>
