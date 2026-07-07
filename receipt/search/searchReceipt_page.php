<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Search Receipt Form</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../receipt.php">Receipt Info</a></span>
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <h3>Search for the receipt or receipts</h3>

        <form action="searchReceipt.php" method="post">

            <TABLE class='form' BORDER="1">
                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT 'date_start' as `date_start`,'date_end' as `date_end`,amount,out_in,description FROM Receipt";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        //Show form for adding an receipt
                        $finfo = $result->fetch_fields();
                        $data = array();
                        while($row = mysqli_fetch_array($result)){
                            $data[] = $row;
                        }

                        showSearchForm($data,$finfo);
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>
            </TABLE><br>
            <input type="submit" action="searchReceipt.php" value="Search Receipts Now">
        </form>
    </body>
</html>
