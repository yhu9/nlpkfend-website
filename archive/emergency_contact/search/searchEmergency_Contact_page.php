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
        <h3>Search for the emergency_contact or emergency_contacts</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        2. emergency_contactID is unique to each individual<br>
        3. date must be in the form yyyy-mm-dd <br>
        <br><br>
        </p>

        <form action="searchEmergency_Contact.php" method="post">
            <select class="selectpicker" name="orderby">
                <option value="" selected>Order By</option>
                <option>emergency_contactID</option>
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
                    include("../queries.php");
                    $sql = emergency_contact_basic();
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for adding an emergency_contact
                        $finfo = $result->fetch_fields();
                        foreach($finfo as $field){
                            if($field->name == "emergency_contactID" or $field->name == "first_name" or $field->name == "last_name"){
                                echo "<tr>";
                                echo "<td><b>$field->name</b></td>";
                                echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                    echo "<option value=\"=\" selected>=</option>";
                                    echo "<option value=\"<\">&lt</option>";
                                    echo "<option value=\">\">&gt</option>";
                                    echo "</select></td>";
                                    if($field->name == 'first_name')
                                        echo "<td align=\"center\"><input type=\"text\" placeholder='first name of child' name=\"text_$field->name\"></td>";
                                    elseif($field->name == 'last_name')
                                        echo "<td align=\"center\"><input type=\"text\" placeholder='last name of child' name=\"text_$field->name\"></td>";
                                    elseif($field->name == "emergency_contactID")
                                        echo "<td align=\"center\"><input type=\"text\" name=\"text_$field->name\"></td>";
                                    echo "</tr>";
                            }
                        }
                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $db->close();
                ?>
            </TABLE><br>
            <input type="submit" action="searchEmergency_Contact.php" value="search">
        </form>
    </body>
</html>
