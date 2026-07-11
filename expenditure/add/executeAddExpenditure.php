<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../expenditure.php">Expenditure Report</a></span>
                <span ><a class="button" href="../add/addExpenditure_page.php">Add Expenditure</a></span>
                <span ><a class="button" href="../search/searchExpenditure_page.php">Search Expenditure</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            $db = connect();
            checkSession();

            //get field values for expenditure to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Expenditure";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();

                //Initialize and create the add expenditure sql statement
                $sql1 = "INSERT INTO Expenditure (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "expenditureID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = mysqli_real_escape_string($db,$_POST[$field->name]);

                        if($field->name == 'time')
                            echo $field->name . "  " . $val . "<br>";

                        if($val != ""){
                            if($is_first == 1){
                                $sql1 .= "$str_fieldname";
                                $sql2 .= "'$val'";
                                $is_first= 0;
                            }
                            else{
                                $sql1 .= ",$str_fieldname";
                                $sql2 .= ",'$val'";
                            }
                        }
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new expenditure
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new expenditure!</h3>";
                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new expenditure: <b>" .mysqli_error($db). "</b>");
                }
            }else{
                echo("sql statement: ".$sql);
                echo "<br>";
                echo("Could not access database fields: <b>" .mysqli_error($db). "</b>");
            }
            
            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    

    </body>
</html>
