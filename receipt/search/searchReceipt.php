<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Search Result</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../receipt.php">Receipt Info</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
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

        //Get field values for receipt
        $sql = "SELECT 'date_start' as `date_start`,'date_end' as `date_end`,amount,out_in,description FROM Receipt";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT * FROM Receipt";
            $sql2 = "";
            $sql3 = "ORDER BY";

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);

                if($field->name == 'time'){
                }else
                    $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--" and $val != "::"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name $eq $val";
                    }elseif($field->name == 'date_start' OR $field->name == 'date_end'){
                        $condition = "date $eq '$val'";
                    }elseif($field->name == 'accountID'){
                        $condition = "fk_accountID $eq $val";
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
            $sql3 = "ORDER BY date DESC,time DESC";

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
                echo "<form method='POST'>";
                echo "<table class='data' align=\"center\">";
                echo "<tr>";
                foreach ($finfo as $field){
                    echo "<th>". $field->name ."</th>";
                }
                echo "</tr>";
                $i=0;
                while($row = mysqli_fetch_array($result)){
                    $id = $row['receiptID'];
                    echo "<tr>";
                    for($i=0; $i < mysqli_num_fields($result); $i++){
                        echo "<td>" . $row[$finfo[$i]->name] ."</td>";
                    }
                    //Button to edit the data. Requires that there be an update file
                    echo "<td><button formaction='../update/search_update.php' name='id' value=$id><img style='width:15px; height:15px;' src=\"/images/edit.png\"></button></td>\n";
                    echo "<td><button formaction='../update/search_delete.php' name='id' value=$id><img style='width:15px; height:15px;' src=\"/images/x_mark.png\"></button></td>\n";
                    echo "</tr>";
                    $i++;
                }
                echo "</table>";
                echo "</form>";
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



