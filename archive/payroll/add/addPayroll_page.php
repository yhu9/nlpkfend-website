<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Payroll Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../payroll.php">Payroll Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../add/addPayroll_page.php">Add Payroll</a></span>
                <span ><a class="button" href="../delete/deletePayroll_page.php">Delete Payroll</a></span>
                <span ><a class="button" href="../update/updatePayroll_page.php">Update Payroll</a></span>
                <span ><a class="button" href="../search/searchPayroll_page.php">Search Payroll</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="executeAddPayroll.php" method="post">
            <h3>Fill Out Your Payroll to Add</h3>
            <p>
            </p>
            <br><br>

            <TABLE class='form' BORDER="1">
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    include("../queries.php");
                    $sql = payroll_basic();
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for adding an payroll
                        $finfo = $result->fetch_fields();
                        foreach($finfo as $field){
                            echo "<tr>";
                            echo "<td><b>$field->name</b></td>";
                            if($field->name == "payrollID" or $field->name == "fk_employeeID" or $field->name == "total_hours" or $field->name == "total_amount"){
                                echo "<td align=\"center\">Do not use</td>";
                            }elseif($field->name == "period_start" or $field->name == "period_end"){
                                echo "<td align='center'>\n";
                                echo "<input type='text' size='4' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                                echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                                echo "<input type='text' size='2' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                                echo "</td>";
                                if($field->flags & 1){
                                    echo "<td><b>REQUIRED</b></td>";
                                }
                            }else{
                                echo "<td align=\"center\"><input type=\"text\" name=\"$field->name\"></td>";
                                if($field->flags & 1){
                                    echo "<td><b>REQUIRED</b></td>";
                                }
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
            <input type="submit" action="executeAddPayroll.php" value="Add Payroll Now">
        </form>
    </body>
</html>
