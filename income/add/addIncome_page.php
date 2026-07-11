<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Add Income Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../income.php">Income Report</a></span>
                <span ><a class="button" href="../add/addIncome_page.php">Add Income</a></span>
                <span ><a class="button" href="../search/searchIncome_page.php">Search Income</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="executeAddIncome.php" method="post">
            <h3>Fill Out Your Income to Add</h3>
            <br><br>

            <TABLE class='form' BORDER="1">
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkAdvancedSession(5);

                    //create query
                    $sql = "SELECT * FROM Income";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for adding an income
                        $finfo = $result->fetch_fields();
                        foreach($finfo as $field){
                            echo "<tr>";
                            echo "<td><b>$field->name</b></td>";
                            if($field->name == "incomeID"){
                                echo "<td align=\"center\">Do not use</td>";
                            }elseif($field->name == 'bank_account'){
                                echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
                                echo "<option value='' selected></option>";
                                echo "<option>US BANK</option>";
                                echo "<option>Alaska Mileage Plan: Visa</option>";
                                echo "<option>Alaska Mileage Plan: Bank of America</option>";
                                echo "<option>AMERICAN EXPRESS</option>";
                                echo "<option>WELLS FARGO</option>";
                                echo "</td>";
                            }elseif($field->name == 'date'){
                                $date = new DateTime();
                                $now = $date->format('Y-m-d');
                                echo "<td align='center'>\n";
                                echo "<input type='date' value=$now name=\"$field->name\">\n";
                                echo "</td>";
                            }elseif($field->name == "time" or $field->name == 'time_in' or $field->name == 'time_out' or $field->name == 'lunch_in' or $field->name == 'lunch_out'){
                                $time = new DateTime();
                                $now = $time->format('H:i');
                                echo "<td nowrap>";
                                echo "<input type='time' name='$field->name' value=$now>";
                                echo "</td>";
                            }elseif($field->name == 'method'){
                                echo "<td align='center'><select class='selectpicker' style='width:100%;' name='$field->name'>";
                                echo "<option value='' selected></option>";
                                echo "<option>cash</option>";
                                echo "<option>credit card</option>";
                                echo "<option>check</option>";
                                echo "</td>";
                            }elseif($field->name == 'out_in'){
                                echo "<td align='center'><select class='selectpicker' style='width:100%; text-align:\"center\";' name='$field->name'>";
                                echo "<option value='-' selected>Income</option>";
                                echo "<option value='+'>Income</option>";
                            }elseif($field->name == 'amount'){
                                echo "<td align=\"center\"> $ <input type=\"text\" name=\"$field->name\"></td>";
                            }else{
                                echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\"></td>";
                            }

                            if($field->flags & 1){
                                echo "<td><b>REQUIRED</b></td>";
                            }
                            echo "</tr>";
                        }
                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $db->close();
                ?>
            </TABLE><br>
            <input type="submit" action="executeAddIncome.php" value="Add Income Now">
        </form>

        <h2 style='margin-top: 200px;color:#fc7b7b;'>DO NOT ADD ANY income FROM BANK ACCOUNT TO BANK ACCOUNT</h2>
    </body>
</html>
