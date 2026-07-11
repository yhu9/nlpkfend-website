<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Emergency_Contact Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../emergency_contact.php">Emergency_Contact Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addEmergency_Contact_page.php">Add Emergency_Contact</a></span>
                <span ><a class="button" href="../delete/deleteEmergency_Contact_page.php">Delete Emergency_Contact</a></span>
                <span ><a class="button" href="../update/updateEmergency_Contact_page.php">Update Emergency_Contact</a></span>
                <span ><a class="button" href="../search/searchEmergency_Contact_page.php">Search Emergency_Contact</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            $db = connect();
            checkSession();

            //get field values for emergency_contact to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Emergency_Contact";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();
                
                //Check if one or more student is found using that query
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $first_name = mysqli_real_escape_string($db,$_POST["first_name"]);
                $last_name = mysqli_real_escape_string($db,$_POST["last_name"]);
                $sql = "SELECT * FROM Student WHERE first_name = '$first_name' and last_name = '$last_name'";
                $check_result = mysqli_query($db,$sql);
                
                if($check_result !== false){
                    //Show the check_results
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $found = mysqli_num_rows($check_result);
                    $check_finfo = $check_result->fetch_fields();
                    echo "<h2 align='center'><u>Student for which emergency_contact is added</u></h2>\n";
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
                        //Initialize and create the add emergency_contact sql statement
                        $sql1 = "INSERT INTO Emergency_Contact (";
                        $sql2 = "VALUES (";
                        $is_first = 1;
                        foreach($finfo as $field){
                            if($field->name != "emergency_contactID"){
                                $str_fieldname = mysqli_real_escape_string($db,$field->name);
                                if($field->type == 10 or $field->name == "phone_number" or $field->name == "cellphone")
                                    $val = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
                                else
                                    $val = mysqli_real_escape_string($db,is_array($_POST[$field->name] ?? '')?'':($_POST[$field->name] ?? ''));

                                if($val != "" and $val != "--"){
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


                        //Create the combined sql statement and execute the addition of the new emergency_contact
                        if (isset($result) && $result instanceof mysqli_result) $result->free();
                        $sql = "$sql1 $sql2";
                        $result = mysqli_query($db,$sql);

                        //Check to make sure the INSERT statement executed
                        if($result !== false){
                            echo "<h3 align=\"center\">Successfully added new emergency_contact!</h3>";
                        }else{
                            echo("sql statement: " .$sql);
                            echo "<br>";
                            echo("Could not add the new emergency_contact: <b>" .mysqli_error($db). "</b>");
                        }

                        //Create the relationship in the many to many table
                        $sql = "INSERT INTO Student_to_Emergency_Contact (fk_studentID,fk_emergency_contactID) VALUES(
                            (SELECT studentID FROM Student WHERE first_name = '$first_name' AND last_name = '$last_name'),(SELECT LAST_INSERT_ID() AS PID))";
                        $result = mysqli_query($db,$sql);
                        if($result !== false){
                            echo "<h3 align=\"center\">Successfully added new emergency_contact relationship!</h3><br>\n";
                        }else{
                            echo("sql statement: " .$sql);
                            echo "<br>";
                            echo("Could not add the new emergency_contact: <b>" .mysqli_error($db). "</b>");
                        }


                    //when there is more than one student or no student found
                    }else{
                        echo "<h2 align='center'>Number of rows found is WRONG. MUST BE ONE</h2>";
                    }
                }
            //when initial query to find students is not found
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
