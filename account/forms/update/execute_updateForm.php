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
                <span ><a class="button" href="../form.php">All Forms</a></span>
                <span ><a class="button" href="../search/searchForm_page.php">Search Form</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../../config.php");
        include("../queries.php");
        $db = connect();

        //initialize variables
        $count = (int)$_POST['count'];
        $formIDs = array();
        $statements = array();
        $statuses = array();
        
        //get form fields
        $tmpsql = "select * from Form LIMIT 1";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();

        //get post variables and set $sql2
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Form";
            $sql2 = "SET ";
            $sql3 = "WHERE";

            //get the id
            $id = mysqli_real_escape_string($db,$_POST["id"]);

            //set change
            $change = false;

            //check if the row is updated at all and create the body of the sql update statement
            if($result !== false){
                $firstpass = 1;
                foreach($finfo as $field){

                    $val = "";
                    $fieldname = "$field->name";
                    $predata = getFieldValue($db,"Form",$field->name,$id);
                    $preval = $predata['data'][0][$field->name];
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
            $sql3 = "WHERE formID = $id";

            //combine and push combined sql to statement
            if($change){
                array_push($formIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $formIDs[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "<h1>Form updated!</h1><br>";
                $formData = getFormByID($db,$id);
                $accountData = getAccountByID($db,$formData['data'][0]['fk_accountID']);
                showData($formData['data'],$formData['fields']);
                showAdvancedData($accountData['data'],$accountData['fields'],"Account Information","/account/viewDetails.php");
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
