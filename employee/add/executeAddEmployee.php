<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../employee.php">Employee Info</a></span>
                <span ><a class="button" href="../add/addEmployee_page.php">Add Employee</a></span>
                <span ><a class="button" href="../search/searchEmployee_page.php">Search Employee</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            $db = connect();

            //get field values for employee to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Employee";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();

                //Initialize and create the add employee sql statement
                $sql1 = "INSERT INTO Employee (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "employeeID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = "";
                        if(strpos($field->name,'date') !== false or $field->name == "DOB"){
                            $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                            if($tmp != "--" and $tmp != '-' and $tmp != ''){
                                $date = DateTime::createFromFormat("m-d-Y",$tmp);
                                $val = $date->format('Y-m-d');
                            }else{
                                $val = "";
                            }
                        }elseif($field->name == "age"){
                            $tmp = mysqli_real_escape_string($db,implode('-',$_POST['DOB']));
                            $date = DateTime::createFromFormat("m-d-Y",$tmp);
                            $DOB = $date->format('Y-m-d');
                            $val = mysqli_real_escape_string($db,"(SELECT TRUNCATE(DATEDIFF(NOW(),'$DOB') / 365.25, 2) as age)");
                            $val = str_replace(array('"'), '', stripslashes($val));
                        }elseif(strpos($field->name,'phone') !== false){
                            $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                            if($tmp != "--")
                                $val = $tmp;
                            else
                                $val = '';
                        }
                        else
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

                //Create the combined sql statement and execute the addition of the new employee
                $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new employee!</h3>";

                    //get last inserted employee
                    $employeeData = getLastInsert($db,"Employee");

                    //show employee data inserted
                    showData($employeeData['data'],$employeeData['fields']);

                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new employee: <b>" .mysqli_error($db). "</b>");
                }
            }else{
                echo("sql statement: ".$sql);
                echo "<br>";
                echo("Could not access database fields: <b>" .mysqli_error($db). "</b>");
            }
            
            $result->free();
            $db->close();
        ?>
    

    </body>
</html>
