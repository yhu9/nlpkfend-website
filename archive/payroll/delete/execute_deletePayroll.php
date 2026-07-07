<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Payroll Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../payroll.php">Payroll Table</a></span>
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../add/addPayroll_page.php">Add Payroll</a></span>
                    <span ><a class="button" href="../delete/deletePayroll_page.php">Delete Payroll</a></span>
                    <span ><a class="button" href="../update/updatePayroll_page.php">Update Payroll</a></span>
                    <span ><a class="button" href="../search/searchPayroll_page.php">Search Payroll</a></span>
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
            $sql = "DELETE FROM Payroll WHERE payrollID = $id";
            array_push($statements,$sql);   
        }

        //execute delete query
        $success = 1;
        foreach($statements as $sql){
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "Records deleted: $count <br>\n";
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
        
        $result->free();
        $db->close();
        ?>
</body>
</html> 


