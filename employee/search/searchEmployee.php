<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>

<div class='title'>
<h1>Search Results</h1>
</div>

<div class="menu_color">
<hr>
    <div class="menu">
        <span ><a class="button" href="../../homepage.php">Homepage</a></span>
        <span ><a class="button" href="../employee.php">Employee Info</a></span>
        <span ><a class="button" href="../add/addEmployee_page.php">Add Employee</a></span>
        <span ><a class="button" href="../search/searchEmployee_page.php">Search Employee</a></span>
        <span ><a class="button" href="../../logout.php">Logout</a></span>
    </div>
<hr>
</div>

    <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //get field values for employee table
        //names from POST are Table column names
        $sql = "SELECT * FROM Employee";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT * FROM Employee";
            $sql2 = "WHERE";
            $sql3 = "ORDER BY last_name ASC, first_name ASC, status ASC";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--"){
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
            if($ORDERBY != ""){
                $sql3 = "ORDER BY $ORDERBY";
            }

            $result->free();
            if($sql2 == "WHERE" and $ORDERBY == "")
                $sql = "SELECT * FROM Employee ORDER BY status ASC, last_name ASC, first_name ASC";
            elseif($ORDERBY != "" AND $sql2 == "WHERE")
                $sql = "SELECT * FROM Employee ORDER BY $ORDERBY DESC";
            else
                $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = mysqli_num_rows($result);

                //get the data
                $finfo = $result->fetch_fields();
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }

                //show the results
                showAdvancedData($data,$finfo,'Search Results',"../viewDetails.php");
            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>
		
</body>
</html> 



