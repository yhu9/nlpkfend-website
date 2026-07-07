<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Receipt Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../receipt.php">Receipt Table</a></span>
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../add/addReceipt_page.php">Add Receipt</a></span>
                    <span ><a class="button" href="../delete/deleteReceipt_page.php">Delete Receipt</a></span>
                    <span ><a class="button" href="../update/updateReceipt_page.php">Update Receipt</a></span>
                    <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
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

        //get field values for receipt table
        //names from POST are Table column names
        $sql = "SELECT * FROM Receipt";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT * FROM Receipt";
            $sql2 = "WHERE";
            $sql3 = "ORDER BY date";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                $val = "";
                if($field->type == 10)
                    $val = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                elseif($field->type == 11)
                    $val = mysqli_real_escape_string($db,implode(':',$_POST[$field->name]));
                else
                    $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--" and $val != "::"){
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
                    if($first_pass)
                        $sql2 .= " $condition";
                    else
                        $sql2 .= " AND $condition";
                    $first_pass = false;
                }


            }
            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            if($ORDERBY == ""){
                $sql3 = "ORDER BY date";
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
            echo "<form action=\"execute_deleteReceipt.php\" method=\"POST\">\n";
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
                    $val = $row["receiptID"];
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
                        $pk_name = "receiptID";
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
            echo "<input type=\"submit\" action=\"execute_deleteReceipt.php\" value=\"Delete This Now!\">\n";
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


