<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Search Income Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../income.php">Income Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addIncome_page.php">Add Income</a></span>
                <span ><a class="button" href="../search/searchIncome_page.php">Search Income</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <h3>Search for the income or incomes</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        2. incomeID is unique to each individual<br>
        3. date must be in form yyyy-mm-dd <br>
        4. out_in must be either (+,-) <br>
        <br><br>
        </p>

        <form action="searchIncome.php" method="post">
            <select class="selectpicker" name="orderby">
                <option value="" selected>Order By</option>
                <option>incomeID</option>
                <option>first_name</option>
                <option>last_name</option>
                <option>date</option>
            </select><br>

            <TABLE class='form' BORDER="1">
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

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
                            if($field->name == "out_in"){
                            echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                echo "<option value=\"=\" selected>=</option>";
                                echo "<option value=\"<\">&lt</option>";
                                echo "<option value=\">\">&gt</option>";
                                echo "</select></td>";
                                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                                    <option value='' selected>Choose Flow of Money</option>
                                    <option>-</option>
                                    <option>+</option>
                                    ";
                            }elseif($field->name == "date"){
                            echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                echo "<option value=\"=\" selected>=</option>";
                                echo "<option value=\"<\">&lt</option>";
                                echo "<option value=\">\">&gt</option>";
                                echo "</select></td>";
                                echo "<td align='center'>\n";
                                echo "<input type='text' size='4' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                                echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                                echo "<input type='text' size='2' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                                echo "</td>";
                            }elseif($field->name == "time"){
                            echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                echo "<option value=\"=\" selected>=</option>";
                                echo "<option value=\"<\">&lt</option>";
                                echo "<option value=\">\">&gt</option>";
                                echo "</select></td>";
                                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                                    <option value='' selected></option>
                                    <option value='12:00:00'>12:00:00</option>
                                    ";
                            }else{
                            echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                echo "<option value=\"=\" selected>=</option>";
                                echo "<option value=\"<\">&lt</option>";
                                echo "<option value=\">\">&gt</option>";
                                echo "</select></td>";
                                echo "<td align=\"center\"><input type=\"text\" name=\"text_$field->name\"></td>";
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
            <input type="submit" action="searchIncome.php" value="search">
        </form>
    </body>
</html>
