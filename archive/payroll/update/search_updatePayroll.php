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
        <h3 align="center"><u>ALL ROWS WILL BE UPDATED WITH NEW VALUES</u></h3>

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
            $sql2 = "WHERE fk_employeeID = employeeID";
            $sql3 = "ORDER BY last_name";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if($field->name == "period_start" or $field->name == "period_end")
                    $val = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
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

            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo "<form action=\"execute_updatePayroll.php\" method=\"POST\">\n";
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

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }

        //SHOW THE FORM for updating the values
        echo "<h2><u>Fill Your Updated Values</u></h2>\n";
        echo "<table class='form' style=\"width:30%\" BORDER=\"1\">\n";
        foreach($finfo as $field){
            echo "<tr>";
            echo "<td width=\"50%\"><b>$field->name</b></td>\n";
            if($field->name == "payrollID" or $field->name == "fk_employeeID" or $field->name == "total_hours" or $field->name == "total_amount"){
                echo "<td width=\"50%\" align=\"center\">UNCHANGEABLE</td>\n";
            }elseif($field->name == "period_start" or $field->name == "period_end"){
                echo "<td align='center'>\n";
                echo "<input type='text' size='4' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                echo "<input type='text' size='2' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                echo "</td>";
            }else{
                echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\"></td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<input type=\"submit\" action=\"execute_updatePayroll.php\" value=\"UPDATE NOW\">\n";
        echo "</form>";
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>



</body>
</html> 


