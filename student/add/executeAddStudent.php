<html>
    <head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
    </head>
    <body>
        <h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="/homepage.php">Homepage</a></span>
                <span ><a class="button" href="/student/student.php">Student Table</a></span>
                <span ><a class="button" href="../cca/cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="/student/add/addStudent_page.php">Add Student</a></span>
                <span ><a class="button" href="/student/search/searchStudent_page.php">Search Student</a></span>
                <span ><a class="button" href="/logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("/var/www/html/config.php");
            include("/var/www/html/student/queries.php");
            $db = connect();

            //get field values for student to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Student LIMIT 1";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();

                //Initialize and create the add student sql statement
                $sql1 = "INSERT INTO Student (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "studentID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = "";
                        $newval = $_POST[$field->name];
                        if($field->name == "age"){
                            $dob = $_POST['DOB'];
                            $val = mysqli_real_escape_string($db,"(SELECT TRUNCATE(DATEDIFF(NOW(),'$dob') / 365.25, 2) as age)");
                            $val = str_replace(array('"'), '', stripslashes($val));
                        }elseif(strpos($field->name,'phone') !== false){
                            $tmp = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
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


                //Create the combined sql statement and execute the addition of the new student
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new student!</h3>";

                    //get last inserted student
                    $studentData = getLastInsert($db,"Student");

                    //check if we are making an account as well. If we are, make an account with the new student information
                    $make_account = $_POST['account'];
                    if($make_account == 'y'){
                        $studentID = $studentData['data'][0]['studentID'];
                        $first_name = $studentData['data'][0]['first_name'];
                        $last_name = $studentData['data'][0]['last_name'];
                        $name = "$last_name, $first_name";
                        $authorization = $studentData['data'][0]['auth_type'];
                        $status = $studentData['data'][0]['status'];
                        $start_date = $studentData['data'][0]['start_date'];
                        $date = new DateTime($start_date);
                        $note = "New Start ". $date->format('m-d-Y');
                        $sql = "INSERT INTO Account (student_1,status,authorization,note) VALUES ('$name','$status','$authorization','$note')";

                        //insert into account and check if it was successful
                        $result = mysqli_query($db,$sql1);
                        if($result !== false){
                            $accountData = getLastInsert($db,"Account");
                            $aid = $accountData['data'][0]['accountID'];
                            $sql = "UPDATE Student SET fk_accountID = $aid WHERE studentID = $studentID";

                            //update the student to the new account created
                            $result = mysqli_query($db,$sql);
                            if($result == false){
                                echo "<h1>";
                                echo("sql statement: " .$sql);
                                echo "<br>";
                                echo("Error: <b>" .mysqli_error($db). "</b>");
                                echo "</h1>";
                            }
                        }
                    }

                    //show student data inserte
                    $aid = $studentData['data'][0]['fk_accountID'];
                    if($aid == '')
                        showAdvancedData($studentData['data'],$studentData['fields'],"Student Information","../viewDetails.php");
                    else
                        showAdvancedData2($studentData['data'],$studentData['fields'],"Student Information","/account/viewDetails.php",$aid);

                }else{
                    echo "<div class='error'>";
                    echo("sql statement: " .$sql);
                    echo "<br><br><br><br>";
                    echo("<u>Could not add the new student: <b>" .mysqli_error($db). "</b></u>");
                    echo "</div>";
                }
            }else{
                echo "<div class='error'>";
                echo("sql statement: ".$sql);
                echo "<br>";
                echo("Could not access database fields: <b>" .mysqli_error($db). "</b>");
                echo "</div>";
            }
            
            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    

    </body>
</html>
