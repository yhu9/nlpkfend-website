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
                <span ><a class="button" href="../../account.php">Account Info</a></span>
                <span ><a class="button" href="../charge.php">All Charges</a></span>
                <span ><a class="button" href="../search/searchCharge_page.php">Search Charge</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../../config.php");
        $db = connect();

        //initialize variables
        $count = (int)$_POST['count'];
        $chargeIDs = array();
        $statements = array();
        $statuses = array();
        
        //get charge fields
        $tmpsql = "select * from Charge";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();

        //get post variables and set $sql2
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Charge";
            $sql2 = "SET ";
            $sql3 = "WHERE";

            //get the id
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);

            //set change
            $change = false;

            //check if the row is updated at all and create the body of the sql update statement
            if($result !== false){
                $firstpass = 1;
                foreach($finfo as $field){
                    $val = "";
                    $fieldname = "$field->name";
                    $predata = getFieldValue($db,"Charge",$field->name,$id);
                    $preval = $predata['data'][0][$field->name];
                    if($field->name == 'time'){
                        $str_time = implode(':',$_POST[$fieldname]);
                        if($str_time == ":"){
                            $newval = "";
                        }else{
                            $ext = mysqli_real_escape_string($db,$_POST["time_ext$id"]);
                            $str_time = "$str_time $ext";
                            $newval = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                        }
                    }else
                        $newval = mysqli_real_escape_string($db,$_POST[$fieldname]);

                    //look for a change
                    if(strpos($field->name,'ID') == false AND $preval != $newval)
                        $change = true;

                    //if change was found in the row
                    if($change and strpos($field->name,'student') == false and strpos($field->name,'ID') == false){
                        $condition = "";
                        if($firstpass != 1)
                            $condition .= ',';
                        else
                            $firstpass = 0;
                        
                        //if field is a numeric
                        if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                            $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                            $field->type == 246)
                        { 
                            $condition .= "$field->name = $newval";
                        //Otherwise it needs quotes
                        }else{
                            $condition .= "$field->name = '$newval'";
                        }
                        
                        $sql2 .= "$condition";
                    }
                }
            }

            //create sql3
            $sql3 = "WHERE chargeID = $id";

            //combine and push combined sql to statement
            if($change){
                array_push($chargeIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $chargeIDs[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "<h1>Charge updated!</h1><br>";
                $chargeData = getChargeByID($db,$id);
                $accountData = getAccountByID($db,$chargeData['data'][0]['fk_accountID']);
                showAdvancedData($accountData['data'],$accountData['fields'],"Account Information","../../viewDetails.php");
            }else{
                echo "Error with sql statement: $sql <br>\n";
            }

            $count++;
        }
        if($count == 0){
            echo "<h1>Nothing Changed</h1>\n";
        }
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
</body>
</html> 
