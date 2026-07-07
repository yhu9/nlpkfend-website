<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    </head>
    <body>
        <h1>Payment Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../account.php">Account Table</a></span>
                <span ><a class="button" href="../payment.php">Payment Table</a></span>
                <span ><a class="button" href="../delete/deletePayment_page.php">Delete Payment</a></span>
                <span ><a class="button" href="../update/updatePayment_page.php">Edit Payment</a></span>
                <span ><a class="button" href="../search/searchPayment_page.php">Search Payment</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <h3>Search for the payment or payments to update</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        </p>
        <br><br>
        <form action="search_updatePayment.php" method="post">
                <select class="selectpicker" name="orderby">
                    <option value="" selected>Order By</option>
                    <option>paymentID</option>
                    <option>date</option>
                    <option>time</option>
                </select><br>
                <?php
                    //connect to database
                    include("../../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    include("../queries.php");
                    $sql = "SELECT * FROM Payment";
                    $result = mysqli_query($db,$sql);
                    $data = array();

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        showSearchForm($data,$fields);                       

                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>
            <br>
            <input type="submit" action="search_updatePayment.php" value="search payments to edit">
        </form>
    </body>
</html>
