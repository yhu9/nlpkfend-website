<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Search Results</h1>
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

        <?php
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //Get field values for expenditure
        $sql = "SELECT * FROM Expenditure";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $sql1 = "SELECT * FROM Expenditure";
            $sql2 = "";
            $sql3 = "ORDER BY";

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

            $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = mysqli_num_rows($result);

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

        $result->free();
        $db->close();
        ?>
		
</body>
</html> 



