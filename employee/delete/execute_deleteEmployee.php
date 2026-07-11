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
                <span ><a class="button" href="../employee.php">Employee Info</a></span>
                <span ><a class="button" href="../add/addEmployee_page.php">Add Employee</a></span>
                <span ><a class="button" href="../search/searchEmployee_page.php">Search Employee</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        $db = connect();

        //get post variables
        $count = (int)$_POST['count'];
        $oneID = $_POST['id'];
        $employeeIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($employeeIDs,$id);
        }
        
        //create the sql statements
        $statements = array();
        foreach($employeeIDs as $id){
            $sql = "DELETE FROM Employee WHERE employeeID = $id";
            array_push($statements,$sql);   
        }

        //execute delete query
        if($oneID != ""){
            $sql = "DELETE FROM Employee WHERE employeeID = $oneID";
            $employeeData = getEmployeeByID($db,$oneID);
            showData($employeeData['data'],$employeeData['fields']);
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

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
</body>
</html> 


