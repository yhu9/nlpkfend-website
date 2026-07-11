<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Add Account Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../account.php">Account Info</a></span>
                <span ><a class="button" href="../payment/payment.php">All Payments</a></span>
                <span ><a class="button" href="../charge/charge.php">All Charges</a></span>
                <span ><a class="button" href="../add/addAccount_page.php">Add Account</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
            <h3>Fill Out Your Account to Add</h3>
            <p>
            <u>NOTE!</u><br>
            1. If empty field, NULL value will be placed<br>
            3. REQUIRED forms must be filled out <br>
            </p> 
            <br><br>

            <?php
                //connect to database
                include("../../config.php");
                include("../queries.php");
                $db = connect();
                checkSession();

                echo "<form action=\"executeAddAccount.php\" method=\"post\">";
                $id = $_POST['id'];
                $name = '';
                if($id != ''){
                    $sdata = getStudentByID($db,$id);
                    showData($sdata['data'],$sdata['fields']);
                    $name = $sdata['data'][0]['last_name'] . ", " . $sdata['data'][0]['first_name'];
                }

                //Query Successful
                if($result !== false){
                    $data = queryAccountBasic($db);

                    //show the add Account Form
                    showAddAccountForm($db,$data['data'],$data['fields'],$sdata);
                    echo "<input type=\"submit\" formaction=\"executeAddAccount.php\" value=\"Add Account Now\">\n";

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
