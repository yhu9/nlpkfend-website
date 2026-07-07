<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../account.php">Account Info</a></span>
                <span ><a class="button" href="../payment/payment.php">All Payments</a></span>
                <span ><a class="button" href="../charge/charge.php">All Charges</a></span>
                <span ><a class="button" href="../add/addAccount_page.php">Add Account</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        $db = connect();

        //initialize variables
        $count = (int)$_POST['count'];
        $statements = array();

        //get account fields
        $tmpsql = "SELECT accountID,student_1,student_2,student_3,student_4,student_5,student_6,student_7,status,authorization,note,autopay,drop_in FROM Account LIMIT 1";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();
        $sid_found = array();

        //create the sql statement to execute
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Account";
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
                    $predata = getFieldValue($db,"Account",$field->name,$id);
                    $fieldname = "$field->name";
                    $newval = $_POST[$fieldname];
                    $preval = $predata['data'][0][$field->name];

                    //look for a change
                    if($preval != $newval)
                        $change = true;

                    //if a change is found create the query
                    if($change){
                        $condition = "";
                        if($firstpass != 1)
                            $sql2 .= ',';
                        else
                            $firstpass = 0;

                        if(strpos($field->name,'student_') !== false){

                            //check what changed from newval and preval to keep relational integrity between student and account table
                            if($newval == '' and $preval != ''){
                                $tokens = preg_split("/, /",$preval);
                                $lname = $tokens[0];
                                $fname = $tokens[1];
                            }elseif($newval != '' and $preval == ''){
                                $sid = $newval;
                                $tmp_sql = "UPDATE Student SET fk_accountID = $id WHERE studentID = $sid";
                                array_push($statements,$tmp_sql);
                                $s_data = getStudentByID($db,$sid);
                                $fname = $s_data['data'][0]['first_name'];
                                $lname = $s_data['data'][0]['last_name'];
                                $name = "$lname, $fname";
                                $newval = $name;
                            }elseif($newval != '' and $preval != ''){
                                //get first and last name of newval
                                $sid = $newval;
                                $s_data = getStudentByID($db,$sid);
                                $fname = $s_data['data'][0]['first_name'];
                                $lname = $s_data['data'][0]['last_name'];
                                $name = "$lname, $fname";
                                $newval = $name;
                                $tmp_sql = "UPDATE Student SET fk_accountID = $id WHERE studentID = $sid";
                                array_push($statements,$tmp_sql);

                                //get first and last name of preval
                                $tokens = preg_split(",\ ",$preval);
                                $pre_lname = $tokens[0];
                                $pre_fname = $tokens[1];
                            }

                            $condition = "$field->name = \"$newval\"";
                            $sql2 .= "$condition";

                        }else{
                            $condition = "$field->name = \"$newval\"";
                            $sql2 .= "$condition";
                        }
                    }
                }
            }

            //create sql3
            $sql3 = "WHERE accountID = $id";

            //combine and push combined sql to statement if there was a change in this row
            if($change){
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql); 
                $newval = $_POST['status'];
                $predata = getFieldValue($db,"Account",'status',$id);
                $preval = $predata['data'][0]['status'];
                if($newval == 'active' and $preval == 'inactive'){
                    array_push($statements,"UPDATE Student SET status = '$newval',end_date = NULL,start_date = DATE(NOW()) WHERE fk_accountID = $id");
                }elseif($newval == 'inactive' and $preval == 'active'){
                    array_push($statements,"UPDATE Student SET status = '$newval',end_date = DATE(NOW()) WHERE fk_accountID = $id");
                }
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $result = mysqli_query($db,$sql);
            if($result == false){
                echo "Error with sql statement: $sql <br>\n";
            }
            $count++;
        }

        //display account data if everything ran smoothly
        $id = mysqli_real_escape_string($db,$_POST["row0"]);
        if($result !== false){
            echo "account updated!";
            $accountData = getAccountByID($db,$id);
            showAdvancedData($accountData['data'],$accountData['fields'],'Account Information',"../viewDetails.php");
        }elseif($count == 0){
            echo "<h1>Nothing Changed</h1>\n";
        }

        $result->free();
        $db->close();
?>

</body>
</html> 
