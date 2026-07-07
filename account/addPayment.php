<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Add Payment</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="account.php">Account Info</a></span>
                <span ><a class="button" href="payment/payment.php">All Payments</a></span>
                <span ><a class="button" href="charge/charge.php">All Charges</a></span>
                <span ><a class="button" href="add/addAccount_page.php">Add Account</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

                <?php
                    //connect to database
                    include("../config.php");
                    include("queries.php");
                    $db = connect();
                    checkAdvancedSession(3);

                    //get account information
                    $id = $_POST['id'];
                    if($id != ''){
                        showAccountByID($db,$id);
                    }

                    //show add payment form
                    echo "<form method='post' action='executeAddPayment.php'>\n";
                    $sql = "SELECT amount,description,date,time,method FROM Payment";
                    $result = mysqli_query($db,$sql);
                    $data = array();
                    if($result !== false){
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;
                        $fields = mysqli_fetch_fields($result);
                    }
                    echo "<br><br><br><br>";
                    showAddForm($data,$fields);
                    echo "<input type='hidden' name='id' value=$id>\n";
                    echo "<input type='submit' value='Add Payment'>\n";
                    echo "</form>\n";

                    $result->free();
                    $db->close();
                ?>
</body>
</html> 
