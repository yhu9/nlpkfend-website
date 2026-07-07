<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Payroll Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../payroll.php">Payroll Table</a></span>
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../add/addPayroll_page.php">Add Payroll</a></span>
                    <span ><a class="button" href="../delete/deletePayroll_page.php">Delete Payroll</a></span>
                    <span ><a class="button" href="../update/updatePayroll_page.php">Update Payroll</a></span>
                    <span ><a class="button" href="../search/searchPayroll_page.php">Search Payroll</a></span>
                    <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>
        <h3 align="center"><u>ALL RECORDS WILL BE DELETED</u></h3>

    <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //get field values for payroll table
        //names from POST are Table column names
        include("../queries.php");
        $sql = payroll_basic(); 
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT first_name,last_name,Payroll.* FROM Employee,Payroll";
            $sql2 = "WHERE employeeID = fk_employeeID";
            $sql3 = "ORDER BY last_name";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if($field->name == "period_start" or $field->name == "period_end")
                    $val = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                else
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
                    
                    $sql2 .= " AND $condition";
                }


            }
            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            if($ORDERBY == ""){
                $sql3 = "ORDER BY last_name";
            }else{
                $sql3 = "ORDER BY $ORDERBY";
            }

            $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo "<form action=\"execute_deletePayroll.php\" method=\"POST\">\n";
            if($result !== false){
                $found = mysqli_num_rows($result);
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
                    $val = $row["payrollID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                echo "<u>$found records found</u>\n";
                echo "<table class='data'>\n";
                echo "<tr>\n";
                foreach ($finfo as $field){
                    echo "<th>". $field->name ."</th>\n";
                }
                echo "</tr>\n";
                foreach($data as $row){
                    echo "<tr>\n";
                    for($i=0; $i < mysqli_num_fields($result); $i++){
                        $pk_name = "payrollID";
                        echo "<td>" . $row[$finfo[$i]->name] ."</td>\n";
                    }
                    echo "</tr>\n";
                }
                echo "</table>\n";
            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

            echo "<br><br>\n";
            echo "<b>Warning! This action cannot be taken back!</b><br>\n";
            echo "<input type=\"submit\" action=\"execute_deletePayroll.php\" value=\"Delete This Now!\">\n";
            echo "</form>\n";

        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>

</body>
</html> 


