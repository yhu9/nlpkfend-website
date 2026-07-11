<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Punch Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                    <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                    <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                    <span ><a class="button" href="../update/updatePunch_page.php">Edit Punch</a></span>
                    <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
                    <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>
        <h3 align="center"><u>ALL RECORDS WILL BE DELETED</u></h3>

    <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();

        //get field values for punch table
        //names from POST are Table column names
        $sql = "SELECT * FROM Punch";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT first_name,last_name,Punch.* FROM Punch,Employee";
            $sql2 = "WHERE employeeID = fk_employeeID ";
            $sql3 = "ORDER BY last_name";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if($field->name == "DOB" or strpos($field->name,'date') !== false){
                    $tmp = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
                    if($tmp != '--'){
                        $date = DateTime::createFromFormat("m-d-Y",$tmp);
                        $val = $date ? $date->format('Y-m-d') : "";
                    }else
                        $val = "";
                }elseif($field->name == 'time'){
                    $str_time = mysqli_real_escape_string($db,implode(':',$_POST['time']));
                    if($str_time == ":"){
                        $val = "";
                    }else{
                        $ext = mysqli_real_escape_string($db,$_POST['time_ext']);
                        $str_time = "$str_time $ext";
                        $val = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                    }
                }elseif($field->name == 'fk_employeeID'){
                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $first_name = mysqli_real_escape_string($db,$_POST["text_first_name"]);
                    $last_name = mysqli_real_escape_string($db,$_POST["text_last_name"]);

                    //search database for employee from first and last name
                    //function in queries.php
                    $data = getEmployeeSearchData($db,$first_name,$last_name);

                    //Show the employee data
                    //function in queries.php
                    echo "<h2 align='center'><u>Employee for which timesheet is shown</u></h2>\n";
                    showData($data['data'],$data['fields']);

                    //get employeeID
                    $employeeID = $data['data'][0]['employeeID'];
                    $val = mysqli_real_escape_string($db,$employeeID);
                }
                else
                    $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--" and $val != ":"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name $eq $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name $eq '$val'";
                    }
                    //Check if its the first condition 
                    $sql2 .= " AND $condition";

                }
            }
            //create line $sql3
            $sql3 = "ORDER BY date,time";

            if (isset($result) && $result instanceof mysqli_result) $result->free();
            if($_POST['text_first_name'] == '' OR $_POST['text_last_name'] == '')
                $sql = "";
            elseif($sql2 == "WHERE employeeID = fk_employeeID ")
                $sql = "SELECT first_name,last_name,Punch.* FROM Employee,Punch WHERE fk_employeeID = employeeID";
            else
                $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                echo "<form action=\"execute_deletePunch.php\" method=\"POST\">\n";
                $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
                $finfo = $result->fetch_fields();
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }
                //save post information
                $tmp = 0;
                foreach($data as $row){
                    $val = $row["punchID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showDeleteablePunch($db,$data,$finfo);
                echo "<br><br>\n";
                echo "<b>Warning! This action cannot be taken back!</b><br>\n";
                echo "<input type='submit' value='Delete Punches'>\n";
                echo "</form>\n";

            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }


        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>

</body>
</html> 


