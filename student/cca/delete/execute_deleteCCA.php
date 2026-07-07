<html>
<head>
<link rel="stylesheet" type="text/css" href="../../../mystyle.css">
<script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Confirmation Page</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../student.php">Student Info</a></span>
                <span ><a class="button" href="../cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="../add/addCCA_page.php">Add Contract</a></span>
                <span ><a class="button" href="../search/searchCCA_page.php">Search Contract</a></span>
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
        $ccaIDs = array();
        for($i = 0; $i < $count; $i++){
            $oneID = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($ccaIDs,$id);
        }

        //execute delete query
        $sql = "DELETE FROM CCA WHERE ccaID = $oneID";
        $ccaData = getCCAByID($db,$oneID);
        $studentData = getStudentByID($db,$ccaData['data'][0]['fk_studentID']);
        $postid = $studentData['data'][0]['fk_accountID'];

        //show cca data to be deleted
        showData($ccaData['data'],$ccaData['fields']);
        echo "<br><br><br>";

        //show student info
        showAdvancedData2($studentData['data'],$studentData['fields'],"Student Information","/account/viewDetails.php",$postid);
        mysqli_query($db,$sql);
        if($result !== false)
            echo "<br>Successfully Deleted Record<br>\n";
        else
            echo "Could not delete the reord!<br>\n";

        $result->free();
        $db->close();
        ?>
</body>
</html> 


