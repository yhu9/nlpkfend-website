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

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //Get field values for emergency_contact
        include("../queries.php");
        $sql = emergency_contact_basic();
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT first_name,last_name,Emergency_Contact.* FROM Student,Emergency_Contact,Student_to_Emergency_Contact";
            $sql2 = "WHERE studentID = fk_studentID AND emergency_contactID = fk_emergency_contactID";
            $sql3 = "ORDER BY last_name";

            //Intialize and create line $sql2
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = "";
                if($field->type == 10 or $field->name == "phone_number" or $field->name == "cellphone")
                    $val = mysqli_real_escape_string($db,implode('-',(array)($_POST[$field->name] ?? [])));
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

            //Show the results
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);

                //get the data
                $finfo = $result->fetch_fields();

                //show the information
                echo "<u>$found records found</u>";
                echo "<table class='data' align=\"center\">";
                echo "<tr>";
                foreach ($finfo as $field){
                    echo "<th>". $field->name ."</th>";
                }
                echo "</tr>";
                $i=0;
                while($row = mysqli_fetch_array($result)){
                    echo "<tr>";
                    for($i=0; $i < mysqli_num_fields($result); $i++){
                        echo "<td>" . $row[$finfo[$i]->name] ."</td>";
                    }
                    echo "</tr>";
                    $i++;
                }
                echo "</table>";
            }
            else{
                echo("Query: ".$sql ."<br>\n");
                echo("Error searching: ". mysqli_error($db));
            }

        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }

        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
		
</body>
</html> 



