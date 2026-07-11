<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Emergency_Contact Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../emergency_contact.php">Emergency_Contact Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addEmergency_Contact_page.php">Add Emergency_Contact</a></span>
                <span ><a class="button" href="../delete/deleteEmergency_Contact_page.php">Delete Emergency_Contact</a></span>
                <span ><a class="button" href="../update/updateEmergency_Contact_page.php">Update Emergency_Contact</a></span>
                <span ><a class="button" href="../search/searchEmergency_Contact_page.php">Search Emergency_Contact</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>
        <p>
        <h3><u>NOTE!</u></h3>
        1. ALL ROWS WILL BE UPDATED WITH NEW VALUES!<br>
        2. dates are in form yyyy-mm-dd<br>
        </p>

    <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //get field values for emergency_contact table
        //names from POST are Table column names
        include("../queries.php");
        $sql = emergency_contact_basic();
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT first_name,last_name,Emergency_Contact.* FROM Student,Emergency_Contact,Student_to_Emergency_Contact";
            $sql2 = "WHERE studentID = fk_studentID AND emergency_contactID = fk_emergency_contactID";
            $sql3 = "ORDER BY last_name";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = "";
                if($field->type == 10 or $field->name == "phone_number" or $field->name == "cellphone")
                    $val = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                else
                    $val = mysqli_real_escape_string($db,$_POST[$val_postname]);
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);

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
            echo "<form action=\"execute_updateEmergency_Contact.php\" method=\"POST\">\n";
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
                    $val = $row["emergency_contactID"];
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
                        $pk_name = "emergency_contactID";
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
            echo "<td width=\"50%\"><b>$field->name</b></td>\n";
            if($field->name == "emergency_contactID" or $field->name == "first_name" or $field->name == "last_name" or $field->name == "fk_studentID"){
                echo "<td width=\"50%\" align=\"center\">UNCHANGEABLE</td>\n";
            }elseif($field->name == "phone_number" or $field->name == "cellphone"){
                echo "<td align='center'>\n";
                echo "<input type='text' size='2' maxlength='3' placeholder='' name=\"$field->name[year]\">\n";
                echo "-";
                echo "<input type='text' size='2' maxlength='3' placeholder='' name=\"$field->name[month]\">\n";
                echo "-";
                echo "<input type='text' size='3' maxlength='4' placeholder='' name=\"$field->name[day]\">\n";
                echo "</td>";
            }elseif($field->type == 10){
                echo "<td align='center'>\n";
                echo "<input type='text' size='4' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                echo "<input type='text' size='2' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                echo "</td>";
            }elseif($field->name == "ok_to_text_msg"){
                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                    <option></option>
                    <option>NO</option>
                    <option>YES</option>
                    ";
                echo "</td>\n";
            }elseif($field->name == "priority"){
                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                    <option value=''>PICK PRIORITY</option>
                    <option>GUARDIAN</option>
                    <option>EMERGENCY</option>
                    <option>ROUTINE</option>
                    <option>EMERGENCY\ROUTINE</option>
                    ";
                echo "</td>\n";
            }elseif($field->name == "custody_arrangement"){
                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                    <option value=''>Any custody arrangments?</option>
                    <option>YES</option>
                    <option>NO</option>
                    <option>NA</option>
                    ";
                echo "</td>\n";
            }else{
                echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\"></td>\n";
            }
            
            echo "</tr>";
        }
        echo "</table>";
        echo "<input type=\"submit\" action=\"execute_updateEmergency_Contact.php\" value=\"UPDATE NOW\">\n";
        echo "</form>";
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>



</body>
</html> 


