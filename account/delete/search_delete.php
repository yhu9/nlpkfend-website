<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Delete Account Page</h1>
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
        <br><br>
        <h1 style="line-height:100%"><b>WARNING! <br><br>THIS ACTION CANNOT BE TAKEN BACK!<br> ALL ASSOCIATED STUDENTS AND PAYMENTS WILL BE DELETED WHEN DELETING AN ACCOUNT. PRESS THE BUTTON TO CONFIRM</b></h1>
</div>
        <hr><br>

    <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkAdvancedSession(3);

        //get field values for account table
        //names from POST are Table column names
        $sql = "SELECT * FROM Account";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $id = $_POST['id'];

            $sql1 = "SELECT * FROM Account";
            $sql2 = "WHERE accountID = $id";
            $finfo = $result->fetch_fields();

            $sql = "$sql1 $sql2";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                echo "<form action=\"execute_deleteAccount.php\" method=\"POST\">\n";
                $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
                $data = getAccountByID($db,$id);

                //save post information
                $tmp = 0;
                foreach($data['data'] as $row){
                    $val = $row["accountID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showDeleteableAccount($db,$data['data'],$data['fields']);
                echo "</form>\n";

            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

            echo "<br><br>\n";

        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>

</body>
</html> 


