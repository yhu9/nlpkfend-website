<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../receipt.php">Receipts</a></span>
                    <span ><a class="button" href="../add/addReceipt_page.php">Add Receipt</a></span>
                    <span ><a class="button" href="../search/searchReceipt_page.php">Search Receipt</a></span>
                    <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        $db = connect();
        checkSession();

        //get post variables
        $count = (int)$_POST['count'];
        $empIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($empIDs,$id);
        }

        //create the sql statements
        $statements = array();
        foreach($empIDs as $id){
            $sql = "DELETE FROM Receipt WHERE receiptID = $id";
            array_push($statements,$sql);   
        }

        //execute delete query
        $success = 1;
        $count = 1;
        foreach($statements as $sql){
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "Record deleted: $count<br>\n";
                $count++;
            }else{
                $success = 0;
                echo "query: $sql <br>\n";
                echo "Error Deleting: ". mysqli_error($db) ."<br>\n";
            }
        }

        //check success
        if($success == 1){
            echo "<h1 align='center'>Successfully deleted records!</h1>";
        }
        else{
            echo "<h1 align='center'>Something went wrong</h1>";
        }
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
</body>
</html> 


