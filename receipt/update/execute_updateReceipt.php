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
                <span ><a class="button" href="../receipt.php">Receipts</a></span>
                <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();
        
        //initialize variables
        $count = (int)$_POST['count'];
        $receiptIDs = array();
        $statements = array();

        //create the sql statements to execute
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Receipt";
            $sql2 = "SET ";
            $sql3 = "WHERE";
           
            //get the id
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);

            //create change flag
            $change = false;

            $query = "SELECT * FROM Receipt WHERE receiptID = $id";
            $result = mysqli_query($db,$query);

            //check if the row is updated at all and create the body of the sql update statement
            if($result !== false){
                $firstpass = 1;
                $finfo = $result->fetch_fields();
                foreach($finfo as $field){
                    $val = "";
                    $fieldname = "$field->name";
                    $predata = getFieldValue($db,"Receipt",$field->name,$id);
                    $preval = $predata['data'][0][$field->name];

                    //check for any special fields
                    if($field->name == "time"){
                        $str_time = implode(':',$_POST['time']);
                        if($str_time == ":"){
                            $newval = "";
                        }else{
                            $postname = "time_ext" . $field->name;
                            $ext = mysqli_real_escape_string($db,$_POST[$postname]);
                            $str_time = "$str_time $ext";
                            $newval = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                        }
                    }else
                        $newval = $_POST[$fieldname];

                    //look for a change
                    if(strpos($field->name,'ID') == false AND $preval != $newval){
                        $change = true;
                        echo "$preval ====== $newval<br>";
                    }

                    //if change was found in the row
                    if($change){
                        $condition = "";
                        if($firstpass != 1)
                            $condition .= ',';
                        else
                            $firstpass = 0;

                        //check if the val is: NULL,NUMERIC,STRING
                        if($newval == ""){
                            $condition .= "$field->name = NULL";
                        }elseif($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                            $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                            $field->type == 246)
                        { 
                            $condition .= "$field->name = $newval";
                        }else{
                            $condition .= "$field->name = \"$newval\"";
                        }
                        
                        $sql2 .= "$condition";
                    }
                }
            }
            //create sql3
            $sql3 = "WHERE receiptID = $id";

            //combine and push combined sql to statement
            if($change){
                array_push($receiptIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }
        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $receiptIDs[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "receipt updated!";
                $receiptData = getReceiptByID($db,$id);
                showData($receiptData['data'],$receiptData['fields']);
            }else{
                echo "Error with sql statement: $sql <br>\n";
                echo "Error Discription: ".mysqli_error($db)."<br>\n";
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
