<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Update Expenditure Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../expenditure.php">Expenditure Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addExpenditure_page.php">Add Expenditure</a></span>
                <span ><a class="button" href="../search/searchExpenditure_page.php">Search Expenditure</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>
        <p>
        <h3><u>NOTE!</u></h3>
        1. ALL ROWS WILL BE UPDATED WITH NEW VALUES!<br>
        2. dates are in form yyyy-mm-dd<br>
        3. status must be (part-time,full-time,terminated)<br>
        </p>

    <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //get field values for expenditure table
        //names from POST are Table column names
        $sql = "SELECT * FROM Expenditure";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT * FROM Expenditure";
            $sql2 = "";
            $sql3 = "ORDER BY date";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if($field->name == "date")
                    $val = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
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

                    if($first_pass)
                        $sql2 .= "WHERE $condition";
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

            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo "<form action=\"execute_updateExpenditure.php\" method=\"POST\">\n";
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
                    $val = $row["expenditureID"];
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
                        $pk_name = "expenditureID";
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
        echo "<u>NOTE!</u><br>\n";
        echo "1. If empty value will not change <br>\n";
        echo "2. ALL RECORDS ABOVE WILL CHANGE! <br><br>\n";
        echo "<table class='form' style=\"width:30%\" BORDER=\"1\">\n";
        foreach($finfo as $field){
            echo "<tr>";
            if($field->name == "expenditureID"){
                echo "<td width=\"50%\"><b>$field->name</b></td>\n";
                echo "<td width=\"50%\" align=\"center\">UNCHANGEABLE</td>\n";
            }else{
                echo "<td><b>$field->name</b></td>\n";
                if($field->name == "out_in"){
                    echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                        <option value='' selected>Choose Flow of Money</option>
                        <option>-</option>
                        <option>+</option>
                        ";
                }elseif($field->name == "date"){
                    echo "<td align='center'>";
                    echo "<input type='text' size='4' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                    echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                    echo "<input type='text' size='2' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                    echo "</td>";
                }elseif($field->name == "time"){
                    echo "<td align='center'>";
                    echo "<input type='text' size='2' maxlength='2' placeholder='HH' name=\"$field->name[HH]\">\n";
                    echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[MM]\">\n";
                    echo "<input type='text' size='2' maxlength='2' placeholder='SS' name=\"$field->name[SS]\">\n";
                    echo "</td>";
                }else{
                    echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\"></td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<input type=\"submit\" action=\"execute_updateExpenditure.php\" value=\"UPDATE NOW\">\n";
        echo "</form>";
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>



</body>
</html> 


