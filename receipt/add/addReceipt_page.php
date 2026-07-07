<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Add Receipt Form</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../receipt.php">Receipt Table</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="executeAddReceipt.php" method="post">
            <h3>Fill Out Your Receipt to Add</h3>
            <p>
            <u>NOTE!</u><br>
            1. If empty field, NULL value will be placed<br>
            2. No dollar sign needed <br>
            3. time and date is automatically recorded from computer time <br>
            4. REQUIRED forms must be filled out <br>
            5. out_in records the income vs output use character (+,-) only <br>
            </p> 
            <br><br>

            <TABLE class='form' BORDER="1">
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT * FROM Receipt";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for adding an receipt
                        $finfo = $result->fetch_fields();
                        foreach($finfo as $field){
                            echo "<tr>";
                            echo "<td><b>$field->name</b></td>";
                            if($field->name == "receiptID" or $field->name == "date" or $field->name == "time"){
                                echo "<td align=\"center\">Do not use</td>";
                            }elseif($field->name == "out_in"){
                                echo "<td>";
                                echo "<select name ='$field->name'>";
                                echo "<option value=''>flow of money</option>";
                                echo "<option>+</option>";
                                echo "<option>-</option>";
                                echo "</select>";
                                echo "</td>";
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

                    $result->free();
                    $db->close();
                ?>
            </TABLE><br>
            <input type="submit" action="executeAddReceipt.php" value="Add Receipt Now">
        </form>
    </body>
</html>
