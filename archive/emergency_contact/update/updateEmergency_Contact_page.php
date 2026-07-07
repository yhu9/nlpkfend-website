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

        <h3>Search for the emergency_contact or emergency_contacts to update</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        </p>
        <br><br>
        <form action="search_updateEmergency_Contact.php" method="post">
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
                            echo "<tr>";
                            echo "<td><b>$field->name</b></td>";
                            echo "<td><select class=\"selectpicker\" name=\"eq_$field->name\">";
                                echo "<option value=\"=\" selected>=</option>";
                                echo "<option value=\"<\">&lt</option>";
                                echo "<option value=\">\">&gt</option>";
                                echo "</select></td>";
                            if($field->name == "phone_number" or $field->name == "cellphone"){
                                echo "<td align='center'>\n";
                                echo "<input type='text' size='3' maxlength='3' placeholder='' name=\"$field->name[area]\">\n";
                                echo "-";
                                echo "<input type='text' size='3' maxlength='3' placeholder='' name=\"$field->name[first]\">\n";
                                echo "-";
                                echo "<input type='text' size='4' maxlength='4' placeholder='' name=\"$field->name[second]\">\n";
                                echo "</td>";
                            }elseif($field->name == "priority"){
                                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                                    <option value=''>PICK PRIORITY</option>
                                    <option>GUARDIAN</option>
                                    <option>EMERGENCY</option>
                                    <option>ROUTINE</option>
                                    <option>EMERGENCY\ROUTINE</option>
                                    ";
                                echo "</td>\n";
                            }elseif($field->type == 10){
                                echo "<td align='center'>\n";
                                echo "<input type='text' size='4' maxlength='4' placeholder='YYYY' name=\"$field->name[year]\">\n";
                                echo "<input type='text' size='2' maxlength='2' placeholder='MM' name=\"$field->name[month]\">\n";
                                echo "<input type='text' size='2' maxlength='2' placeholder='DD' name=\"$field->name[day]\">\n";
                                echo "</td>";
                            }elseif($field->name == "ok_to_text_msg"){
                                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                                    <option></option>
                                    <option>NO</option>
                                    <option>YES</option>
                                    ";
                                echo "</td>\n";
                            }elseif($field->name == "custody_arrangement"){
                                echo "<td align='center'><select style='width:100%;' class='selectpicker' name='text_$field->name'>
                                    <option value=''>Any custody arrangments?</option>
                                    <option>YES</option>
                                    <option>NO</option>
                                    <option>NA</option>
                                    ";
                                echo "</td>\n";
                            }elseif($field->name == 'first_name'){
                                echo "<td align=\"center\"><input type=\"text\" placeholder='first name of child' name=\"text_$field->name\"></td>";
                            }elseif($field->name == 'last_name'){
                                echo "<td align=\"center\"><input type=\"text\" placeholder='last name of child' name=\"text_$field->name\"></td>";
                            }else{
                                echo "<td align=\"center\"><input type=\"text\" name=\"text_$field->name\"></td>";
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
            </TABLE>
            <br>

            <input type="submit" action="search_updateEmergency_Contact.php" value="search">
        </form>
    </body>
</html>
