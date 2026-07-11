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

        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();

            //get field values for account to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Account";
            $result = mysqli_query($db,$sql);
            $values = array();
            $queue = array();
            if($result !== false){
                $finfo = $result->fetch_fields();

                //Initialize and create the add account sql statement
                $sql1 = "INSERT INTO Account (";
                $sql2 = "VALUES (";
                $count = 1;
                $firsttime = True;
                foreach($finfo as $field){
                    if($field->name != "accountID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = "";
                        $fname = $field->name;
                        if(strpos($field->name,'student_') !== false){
                            $sid = $_POST[$fname];
                            if($sid != ''){
                                $studentData = getStudentByID($db,$sid);
                                $queue[] = $sid;
                                $first_name = $studentData['data'][0]['first_name'];
                                $last_name = $studentData['data'][0]['last_name'];
                                $name = "$last_name, $first_name";
                                $val = $name;
                            }else{
                                $val = '';
                            }
                        }else{
                            $val = $_POST[$fname];
                        }

                        if($val != ""){
                            if($firsttime == False){
                                $sql1 .= ",";
                                $sql2 .= ",";
                            }

                            if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                                $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                                $field->type == 246){
                                    $sql1 .= "$str_fieldname";
                                    $sql2 .= "$val";
                            }else{
                                $sql1 .= "$str_fieldname";
                                $sql2 .= "\"$val\"";
                            }

                            $firsttime = False;
                        }
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new account
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new account!</h3>";

                    //get last inserted account
                    $accountData = getLastInsert($db,"Account");

                    //keep relational integrity
                    $aid = $accountData['data'][0]['accountID'];
                    foreach($queue as $sid){
                        $query = "UPDATE Student SET fk_accountID = $aid WHERE studentID = $sid";
                        $result = mysqli_query($db,$query);
                        if($result !== false)
                            echo "Student Updated! <br>";
                    }

                    //show account data inserted
                    showAdvancedData($accountData['data'],$accountData['fields'],'Account Information',"../viewDetails.php");
                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new account: <b>" .mysqli_error($db). "</b>");
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
