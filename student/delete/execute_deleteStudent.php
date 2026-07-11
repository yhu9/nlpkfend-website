<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
<script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Confirmation Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../student.php">Student Table</a></span>
                <span ><a class="button" href="../cca/cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="../add/addStudent_page.php">Add Info</a></span>
                <span ><a class="button" href="../search/searchStudent_page.php">Search Student</a></span>
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
        $studentIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($studentIDs,$id);
        }

        //create the sql statements
        $statements = array();
        foreach($studentIDs as $id){
            //add sql statement to delete
            $sql = "DELETE FROM Student WHERE studentID = $id";
            array_push($statements,$sql);

            //add sql statement to update account
            $studentData = getStudentByID($db,$oneID);
            $aid = $studentData['data'][0]['fk_accountID'];
            $first_name = $studentData['data'][0]['first_name'];
            $last_name = $studentData['data'][0]['last_name'];
            $full_name = $last_name . ', ' . $first_name;

        }

        //execute delete query
        if($oneID != ""){

            mysqli_query($db,$sql);
            if($result !== false){
                if($aid == '')
                    showData($studentData['data'],$studentData['fields']);
                else
                    showAdvancedData2($studentData['data'],$studentData['fields'],"Student Information", "/account/viewDetails.php",$aid);
                echo "<br>Successfully Deleted Record<br>\n";
            }else{
                showAdvancedData2($studentData['data'],$studentData['fields'],"Student Information" ,"/account/viewDetails.php",$aid);
                echo "<p class='error'> Could not delete the record!<br>\n</p>";
            }

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


