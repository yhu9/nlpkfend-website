<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../account.php">Account Info</a></span>
                <span ><a class="button" href="../payment.php">All Payments</a></span>
                <span ><a class="button" href="../search/searchPayment_page.php">Search Payment</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../../config.php");
        $db = connect();

        //get post variables
        $count = (int)$_POST['count'];
        $oneID = $_POST['id'];
        $paymentIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($paymentIDs,$id);
        }

        //create the sql statements
        $statements = array();
        foreach($paymentIDs as $id){
            $sql = "DELETE FROM Payment WHERE paymentID = $id";
            array_push($statements,$sql);   
        }

        //execute delete query
        if($oneID != ""){
            $sql = "DELETE FROM Payment WHERE paymentID = $oneID";
            $paymentData = getPaymentByID($db,$oneID);
            $accountData = getAccountByID($db,$paymentData['data'][0]['fk_accountID']);
            showAdvancedData($accountData['data'],$accountData['fields'],"Account Information","../../viewDetails.php");
            mysqli_query($db,$sql);
            if($result !== false)
                echo "<br>Successfully Deleted Record<br>\n";
            else
                echo "Could not delete the reord!<br>\n";

        }else{
            $i = 1;
            foreach($statements as $sql){
                $result = mysqli_query($db,$sql);
                if($result !== false){
                    echo "Successfully Deleted Record $i<br>\n";
                }else{
                    echo "query: $sql <br>\n";
                    echo "Error Deleting row $i: ". mysqli_error($db) ."<br>\n";
                }
                $i++;
            }
        }

        $result->free();
        $db->close();
        ?>
</body>
</html> 
