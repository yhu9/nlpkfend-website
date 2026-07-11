<html>
    <head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    </head>
    <body>
        <h1>Add Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="/homepage.php">Homepage</a></span>
                <span ><a class="button" href="/account/account.php">Account Info</a></span>
                <span ><a class="button" href="/logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
            <p>
            <br><br>

            <?php
                //connect to database
                include("../../../config.php");
                include("../queries.php");
                $db = connect();
                checkSession();

                //create the add form page
                echo "<form action=\"executeAddForm.php\" method=\"POST\" enctype='multipart/form-data'>\n";

                //continue to send primary key id as post information
                $id = $_POST['id'];
                echo "<input type='hidden' name='id' value=$id>";

                //Query Successful
                $result = mysqli_query($db,"SELECT fk_accountID,date,file,description FROM Form LIMIT 1");
                if($result !== false){
                    $fields = mysqli_fetch_fields($result);

                    //initialize variables
                    showAddForm('',$fields);

                    echo "<input type=\"submit\" formaction=\"executeAddForm.php\" value=\"UPLOAD FORM NOW\">\n";

                //Query FAILED
                }else{
                    echo("Error Description: ".mysqli_error($db));
                }
                echo "</form>\n";

                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $db->close();
            ?>
    </body>
</html>

