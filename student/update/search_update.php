<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <script type='text/javascript'>
    function valueChanger(id1,id2){
        document.getElementById(id1).value = document.getElementById(id2).value;
    }
    </script>
</head>
<body>
<h1>Update Student Page</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../student.php">Student Info</a></span>
                <span ><a class="button" href="../cca/cca.php">Contracts/Authorizations</a></span>
                <span ><a class="button" href="../add/addStudent_page.php">Add Student</a></span>
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
        checkSession();

        //get field values for student table
        //get pid from post
        $pid = $_POST['id'];

        $sql1 = "SELECT studentID,last_name,first_name,sex,DOB,start_date,physical_date,room,auth_type,tuition_type,status,parent1,parent2,mailing_address,email,phone_number,allergy,end_date,allow_picture,note FROM Student";
        $sql2 = "WHERE studentID = $pid";
        $sql = "$sql1 $sql2";

        $result = mysqli_query($db,$sql);
        //Show the results and save the values as hidden fields
        //hidden fields:
        //count
        //row1,row2,row3,row4,...
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if($result !== false){
            $finfo = $result->fetch_fields();
            echo "<form action=\"execute_updateStudent.php\" method=\"POST\" enctype='multipart/form-data'>\n";
            $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
            echo "<input type='hidden' name='count' value=$found>\n";

            //get the data
            $data = array();
            while($row = mysqli_fetch_array($result)){
                $data[]=$row;
            }

            //save post information
            $tmp = 0;
            echo "<input type='hidden' name=\"row$tmp\" value=$pid>\n";

            //show the results
            showEditableData2($data,$finfo);
            echo "<input type='submit' value='Update Values'>\n";
            echo "</form>\n";
        }
        else{
            echo("Query: $sql <br>");
            echo("Error searching: ". mysqli_error($db));
        }

        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
?>


</body>
</html> 

