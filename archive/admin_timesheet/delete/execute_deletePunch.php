<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Punch Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../timesheet.php">Punch Table</a></span>
                    <span ><a class="button" href="../add/addPunch_page.php">Add Punch</a></span>
                    <span ><a class="button" href="../delete/deletePunch_page.php">Delete Punch</a></span>
                    <span ><a class="button" href="../update/updatePunch_page.php">Edit Punch</a></span>
                    <span ><a class="button" href="../search/searchPunch_page.php">Search Punch</a></span>
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
        $oneID = $_POST['id'];
        $punchIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($punchIDs,$id);
        }

        //create the sql statements
        $statements = array();
        foreach($punchIDs as $id){
            $sql = "DELETE FROM Punch WHERE punchID = $id";
            array_push($statements,$sql);   
        }

        //execute delete query
        if($oneID != ""){
            $sql = "DELETE FROM Punch WHERE punchID = $oneID";
            $punchData = getPunchByID($db,$oneID);
            showData($punchData['data'],$punchData['fields']);
            mysqli_query($db,$sql);
            if($result !== false)
                echo "<br>Successfully Deleted Record<br>\n";
            else
                echo "Could not delete the reord!<br>\n";

        }else{
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
        }

        $result->free();
        $db->close();
        ?>
</body>
</html> 


