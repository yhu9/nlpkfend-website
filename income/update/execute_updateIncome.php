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
                <span ><a class="button" href="../income.php">Income Report</a></span>
                <span ><a class="button" href="../add/addIncome_page.php">Add Income</a></span>
                <span ><a class="button" href="../search/searchIncome_page.php">Search Income</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        include("../../upload.php");
        $db = connect();

        //initialize variables
        $count = (int)$_POST['count'];
        $incomeIDs = array();
        $statements = array();

        //get income fields
        $tmpsql = "SELECT * FROM Income";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();

        //create the sql statements to execute
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Income";
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
                    $fieldname = "$field->name";
                    $predata = getFieldValue($db,"Income",$field->name,$id);
                    $preval = $predata['data'][0][$field->name];
                    if($field->name == 'file_location'){
                        /*
                        $s_data = getIncomeByID($db,$id);
                        $sid = $s_data['last_name']."_".$s_data['first_name']."$id";
                        $file = $_FILES[$name]['name'];

                        if($preval == '')
                            $newval = '';
                        else
                            $newval = "resources/nlp_data/income/$id/$file";

                        //check for any files to upload
                        $name = "fileToUpload$id";
                        $target_dir = "../../resources/nlp_data/income/$id/";
                        if($_FILES[$name]["name"] != '')
                            uploader($name,$target_dir);
                         */
                    }else
                        $newval = $_POST[$fieldname];

                    //look for a change
                    if(strpos($field->name,'ID') == false AND $preval != $newval){
                        $change = true;
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
            $sql3 = "WHERE incomeID = $id";

            //combine and push combined sql to statement
            if($change){
                array_push($incomeIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $incomeIDs[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "<h2>income updated!</h2><br>";
                $incomeData = getIncomeByID($db,$id);
                showData($incomeData['data'],$incomeData['fields']);
            }else{
                echo "Error with sql statement: $sql <br>\n";
                echo "Error Discription: ".mysqli_error($db)."<br>\n";
            }

            $count++;
        }
        if($count == 0){
            echo "<h1>Nothing Changed</h1>\n";
        }

        $result->free();
        $db->close();
        ?>
</body>
</html> 
