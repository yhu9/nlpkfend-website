<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    </head>
    <body>
        <h1>Charge Search Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../account.php">Account Info</a></span>
                <span ><a class="button" href="../charge.php">All Charges</a></span>
                <span ><a class="button" href="../search/searchCharge_page.php">Search Charge</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <h3>Search for the charge or charges to update</h3>
        <p>
        <u>NOTE!</u><br>
        1. Empty fields will not be used<br>
        </p>
        <br><br>
        <form action="searchCharge.php" method="post">
                <select class="selectpicker" name="orderby">
                    <option value="" selected>Order By</option>
                    <option>chargeID</option>
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
                    $sql = "SELECT 'account id' as `accountID`, 'date_start' as `date_start`,'date_end' as `date_end`,amount,description FROM Charge";
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

                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $db->close();
                ?>
            <br>
            <input type="submit" action="searchCharge.php" value="search charges to delete">
        </form>
    </body>
</html>
