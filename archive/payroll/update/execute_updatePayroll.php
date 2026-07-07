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

        //Initialize sql statements
        $sql1 = "UPDATE Payroll";
        $sql2 = "SET ";
        $sql3 = "WHERE";

        //get post variables and set $sql2
        $count = (int)$_POST['count'];
        $empIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($empIDs,$id);
        }
        $sql = "SELECT * FROM Payroll";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $finfo = $result->fetch_fields();
            $firstpass = 1;
            foreach($finfo as $field){
                $val = mysqli_real_escape_string($db,$_POST[$field->name]);
                if($val != ""){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name = $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name = '$val'";
                    }

                    if($firstpass ==1){
                        $sql2 .= "$condition";
                        $firstpass = 0;
                    }else{
                        $sql2 .= ",$condition";
                    }
                }
            }
        }

        //create $sql3 and create the different statements
        //$sql = $sql1 + $sql2 + $sql3
        $statements = array();
        foreach($empIDs as $id){
            $sql3 = "WHERE payrollID = $id";
            $sql = "$sql1 $sql2 $sql3";
            array_push($statements,$sql);   
        }

        //execute update query
        $success = 1;
        foreach($statements as $sql){
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "Records Changed: $count <br>\n";
            }else{
                $success = 0;
                echo "query: $sql <br>\n";
                echo "Error Updating: ". mysqli_error($db) ."<br>\n";
            }
        }

        //check success
        if($success == 1){
            echo "<h1 align='center'>Successfully updated records!</h1>";
        }
        else{
            echo "<h1 align='center'>Something went wrong</h1>";
        }
        
        $result->free();
        $db->close();
        ?>
</body>
</html> 
