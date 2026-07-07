<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
    </head>
    <body>
        <h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../student.php">Student Info</a></span>
                <span ><a class="button" href="../cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="../add/addCCA_page.php">Add Contract</a></span>
                <span ><a class="button" href="../search/searchCCA_page.php">Search Contract</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
        //connect to database
        include("/var/www/html/config.php");
        include("/var/www/html/student/cca/queries.php");
        $db = connect();

        $rowcount = $_POST['count'];

        //get field names for cca to add
        //names from POST are Table column names
        $sql = "SELECT * FROM CCA";
        $result = mysqli_query($db,$sql);
        $values = array();
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql_list = array();

            //Initialize and create the add cca sql statement for each row and push the sql statements into list
            for($i = 1; $i <= $rowcount AND $rowcount != 0; $i++){
                $sql1 = "INSERT INTO CCA (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "ccaID"){
                        $postname = mysqli_real_escape_string($db,$field->name) . "_$i";
                        $val = mysqli_real_escape_string($db,$_POST[$postname]);
                        
                        if($val != "" and $val != "--"){
                            if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                                $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                                $field->type == 246){
                                if($is_first == 1){
                                    $sql1 .= "$field->name";
                                    $sql2 .= "$val";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$field->name";
                                    $sql2 .= ",$val";
                                }
                            }else{
                                if($is_first == 1){
                                    $sql1 .= "$field->name";
                                    $sql2 .= "\"$val\"";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$field->name";
                                    $sql2 .= ",\"$val\"";
                                }
                            } 
                        }
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new cca
                $result->free();
                $sql = "$sql1 $sql2";
                $sql_list[] = $sql;
                $result = mysqli_query($db,$sql);
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new cca!</h3>";

                    //get last inserted cca
                    $ccaData = getLastInsert($db,"CCA");
                    $studentData = getStudentByID($db,$ccaData['data'][0]['fk_studentID']);
                    $postid = $studentData['data'][0]['fk_accountID'];

                    //show cca data inserted
                    showAdvancedData2($studentData['data'],$studentData['fields'],"Student Information","/account/viewDetails.php",$postid);
                }
                else{
                    //ERROR SCRIPT FOR OVERLAPPING DATES
                    //make the query
                    foreach($finfo as $field){
                        $postname = mysqli_real_escape_string($db,$field->name) . "_$i";
                        $val = mysqli_real_escape_string($db,$_POST[$postname]);
                        if($field->name == 'start_date'){
                            $cond1 = "'$val'".">= CCA.start_date";
                            $cond2 = "'$val'"."<= CCA.end_date";
                        }elseif($field->name == 'end_date'){
                            $cond3 = "'$val'".">= CCA.start_date";
                            $cond4 = "'$val'"."<= CCA.end_date";
                        }
                    }
                    $sid_postname = "fk_studentID_$i";
                    $sid = $_POST[$sid_postname];
                    $sql = "SELECT fk_accountID as aid,ccaID,last_name,first_name,CCA.start_date as 'Contract Start Date',CCA.end_date as 'Contract Start Date' FROM CCA INNER JOIN Student ON fk_studentID = studentID
                        WHERE (($cond1 AND $cond3) OR ($cond2 AND $cond4)) AND fk_studentID = $sid";

                    echo "<p class='error'>";
                    echo "<br>";
                    echo("Could not add the new cca: " .mysqli_error($db));
                    echo "</p>";

                    //run the query for the errors and show the overlapping contracts
                    $result = mysqli_query($db,$sql);
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;
                        $studentData = getStudentByID($db,$sid);
                        $aid = $studentData['data'][0]['fk_accountID'];
                        showAdvancedData2($data,$fields,"Conflicting Contract", "/account/viewDetails.php",$aid);
                    }else{
                        echo "FATAL ERROR: ". mysqli_error($db);
                    }
                }
            }
        }
        $result->free();
        $db->close();
        ?>
    </body>
</html>

