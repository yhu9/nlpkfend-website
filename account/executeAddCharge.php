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
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="account.php">Account Info</a></span>
                <span ><a class="button" href="payment/payment.php">All Payments</a></span>
                <span ><a class="button" href="charge/charge.php">All Charges</a></span>
                <span ><a class="button" href="add/addAccount_page.php">Add Account</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../config.php");
            $db = connect();

            //get field values for charge to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Charge";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();
                
                //Initialize and create the add charge sql statement
                $sql1 = "INSERT INTO Charge (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "chargeID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = mysqli_real_escape_string($db,$_POST[$field->name]);
                        if($field->name == "time"){
                            $str_time = implode(':',$_POST['time']);
                            if($str_time == ":"){
                                $val = "";
                            }else{
                                $postname = "time_ext" . $field->name;
                                $ext = mysqli_real_escape_string($db,$_POST[$postname]);
                                $str_time = "$str_time $ext";
                                $val = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                            }
                        }elseif($field->name == "fk_accountID"){
                            $id = $_POST['id'];
                            $val = $id;
                        }
                        if($is_first == 1){
                            $is_first = 0;
                        }else{
                            $sql1 .=',';
                            $sql2 .=',';
                        }

                        $sql1 .= $str_fieldname;
                        if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                            $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR $field->type == 246){
                            $sql2 .= "$val";
                        }else
                            $sql2 .= "'$val'";
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new charge
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new charge!</h3>";

                    //get last inserted charge
                    $chargeData = getLastInsert($db,"Charge");
                    $accountData = getAccountByID($db,$chargeData['data'][0]['fk_accountID']);

                    //show charge data inserted
                    showAdvancedData($accountData['data'],$accountData['fields'],'Account Updated',"viewDetails.php");
                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new charge: <b>" .mysqli_error($db). "</b>");
                }

            //when initial query to find students is not found
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
