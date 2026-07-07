<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Punch Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                <span ><a class="button" href="../update/updatePunch_page.php">Update Punch</a></span>
                <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="search_deletePunch.php" method="post">
            <h3>Search for the punch or punchs to delete</h3>
            <u>NOTE!</u><br>
            1. Empty fields will not be used<br>
            <br><br>
                <select class="selectpicker" name="orderby">
                    <option value="" selected>Order By</option>
                    <option>punchID</option>
                    <option>first_name</option>
                    <option>last_name</option>
                    <option>start_date</option>
                </select><br>

            <TABLE class='form' BORDER="1">

                <?php
                    //connect to database
                    include("../../config.php");
                    include("../queries.php");
                    $db = connect();

                    //create query
                    $sql = minPunchQuery();
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for deleting a punch
                        $finfo = $result->fetch_fields();
                        foreach($finfo as $field){
                            echo "<tr>";
                            echo "<td><b>$field->name</b></td>";
                            echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                echo "<option value=\"=\" selected>=</option>";
                                echo "<option value=\"<\">&lt</option>";
                                echo "<option value=\">\">&gt</option>";
                                echo "</select></td>";
                            if($field->name == 'fk_employeeID' or $field->name == 'fk_payrollID'){
                                echo "<td align=\"center\">Do not use</td>\n";
                            }elseif($field->name == "time"){
                                echo "<td>";
                                echo "<input style='width:100px' type='text' maxlength='2' placeholder='HH' name=\"$field->name[hour]\">\n";
                                echo ":";
                                echo "<input style='width:100px' type='text' maxlength='2' placeholder='MM' name=\"$field->name[min]\">\n";
                                echo "<input type='checkbox' name='time_ext' value='AM'>AM\n";
                                echo "<input type='checkbox' name='time_ext' value='PM'>PM\n";
                                echo "</td>";
                            }elseif($field->name == "type"){
                                echo "<td align='center'><select class='selectpicker' style='width:100%;' name='text_$field->name'>
                                    <option value='' selected>Choose type</option>
                                    <option>clock in</option>
                                    <option>clock out</option>";
                                echo "</td>";
                            }elseif($field->name == "date" or $field->name == "end_date" or $field->name == "DOB" or $field->name == "physical_date"){
                                echo "<td align='center'>\n";
                                echo "<input style='width:100px;' type='text' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                                echo "<input style='width:100px;' type='text' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                                echo "<input style='width:100px;' type='text' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                                echo "</td>";
                            }else{
                                echo "<td align=\"center\"><input type=\"text\" name=\"text_$field->name\"></td>";
                            }
                            if($field->name == 'first_name' or $field->name == 'last_name'){
                                echo "<td><b>REQUIRED</b></td>";
                            }
                            echo "</tr>";
                        }
                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>
            </TABLE><br>
            <input type="submit" action="search_deletePunch.php" value="Search for punches to delete">
        </form>
    </body>
</html>
