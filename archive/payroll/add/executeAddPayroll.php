<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Payroll Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../payroll.php">Payroll Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addPayroll_page.php">Add Payroll</a></span>
                <span ><a class="button" href="../delete/deletePayroll_page.php">Delete Payroll</a></span>
                <span ><a class="button" href="../update/updatePayroll_page.php">Update Payroll</a></span>
                <span ><a class="button" href="../search/searchPayroll_page.php">Search Payroll</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            $db = connect();
            checkSession();

            //get field values for payroll to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Payroll";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();
                
                //Check if one or more employee is found using that query
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $first_name = mysqli_real_escape_string($db,$_POST["first_name"]);
                $last_name = mysqli_real_escape_string($db,$_POST["last_name"]);
                $sql = "SELECT * FROM Employee WHERE first_name = '$first_name' and last_name = '$last_name'";
                $check_result = mysqli_query($db,$sql);
                
                if($check_result !== false){
                    //Show the check_results
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $found = mysqli_num_rows($check_result);
                    $check_finfo = $check_result->fetch_fields();
                    echo "<h2 align='center'><u>Employee for which payroll is added</u></h2>\n";
                    echo "<u>$found records found</u>";
                    echo "<table class='data' align=\"center\">";
                    echo "<tr>";
                    foreach ($check_finfo as $field){
                        echo "<th>". $field->name ."</th>";
                    }
                    echo "</tr>";
                    $i=0;
                    while($row = mysqli_fetch_array($check_result)){
                        echo "<tr>\n";
                        for($i=0; $i < mysqli_num_fields($check_result); $i++){
                            echo "<td>" . $row[$check_finfo[$i]->name] ."</td>\n";
                        }
                        echo "</tr>\n";
                        $i++;
                    }
                    echo "</table>";
                        
                    //CHECK to see if the number of rows found is only 1 otherwise something is wrong
                    if(mysqli_num_rows($check_result) == 1){
                        //Initialize and create the add payroll sql statement
                        $sql1 = "INSERT INTO Payroll (";
                        $sql2 = "VALUES (";
                        $is_first = 1;
                        foreach($finfo as $field){
                            if($field->name != "payrollID"){
                                $str_fieldname = mysqli_real_escape_string($db,$field->name);
                                $val = mysqli_real_escape_string($db,$_POST[$field->name]);
                                if($str_fieldname == "period_start" or $str_fieldname == "period_end")
                                    $val = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
                                else
                                    $val = mysqli_real_escape_string($db,$_POST[$field->name]);

                                if($field->name == "fk_employeeID"){
                                    $sql1 .= ",$field->name";
                                    $sql2 .= ",(SELECT employeeID FROM Employee WHERE first_name = '$first_name' AND last_name = '$last_name')";
                                }elseif($field->name == "total_amount"){
                                    $h1 = floatval($_POST["regular_hours"]);
                                    $h2 = floatval($_POST["vacation_hours"]);
                                    $h3 = floatval($_POST["holiday_hours"]);
                                    $h_total = $h1 + $h2 + $h3;
                                    $benefit = floatval($_POST["benefit"]);
                                    $pay_rate = floatval($_POST["pay_rate"]);
                                    $total_amount = $h_total * $pay_rate + $benfit;
                                    $total_amount_string = mysqli_real_escape_string($db,$total_amount);
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",$total_amount_string";
                                }elseif($field->name == "total_hours"){
                                    $sql1 .= ",$str_fieldname";
                                    $h1 = floatval($_POST["regular_hours"]);
                                    $h2 = floatval($_POST["vacation_hours"]);
                                    $h3 = floatval($_POST["holiday_hours"]);
                                    $h_total = $h1 + $h2 + $h3;
                                    $h_total_string = mysqli_real_escape_string($db,$h_total);
                                    $sql2 .= ",$h_total_string";
                                }
                                elseif($val != "" and $val != "--"){
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
                        $sql1 .= ")";
                        $sql2 .= ")";


                        //Create the combined sql statement and execute the addition of the new payroll
                        if (isset($result) && $result instanceof mysqli_result) $result->free();
                        $sql = "$sql1 $sql2";
                        $result = mysqli_query($db,$sql);

                        //Check to make sure the INSERT statement executed
                        if($result !== false){
                            echo "<h3 align=\"center\">Successfully added new payroll!</h3>";
                        }else{
                            echo("sql statement: " .$sql);
                            echo "<br>";
                            echo("Could not add the new payroll: <b>" .mysqli_error($db). "</b>");
                        }

                    //when there is more than one employee or no employee found
                    }else{
                        echo "<h2 align='center'>Number of rows found is WRONG. MUST BE ONE</h2>";
                    }
                }
            //when initial query to find employees is not found
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
