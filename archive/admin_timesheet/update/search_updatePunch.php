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
                <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                <span ><a class="button" href="../update/updatePunch_page.php">Update Punch</a></span>
                <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

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
            $sql1 = "SELECT * FROM Punch";
            $sql2 = "WHERE";
            $sql3 = "ORDER BY date,time";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if($field->name == "date" or $field->name == "start_date" or $field->name == "DOB" or $field->name == "physical_date")
                    $val = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
                elseif($field->name == 'time'){
                    $str_time = mysqli_real_escape_string($db,implode(':',(array)($_POST['time'] ?? [])));
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

                    if($first_pass)
                        $sql2 .= " $condition";
                    else
                        $sql2 .= " AND $condition";
                    $first_pass = false;
                }


            }
            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            $sql3 = "ORDER BY date,time";

            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo "<form action=\"execute_updatePunch.php\" method=\"POST\">\n";
            if($result !== false){
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

                showEditableData($data,$finfo);

            }

            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }
            echo "<br><br>\n";

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>



</body>
</html> 


