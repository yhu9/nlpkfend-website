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

        //initialize variables
        $count = (int)$_POST['count'];
        $studentIDs = array();
        $statements = array();

        //get student fields
        $tmpsql = "SELECT studentID,last_name,first_name,sex,DOB,start_date,physical_date,room,auth_type,tuition_type,status,parent1,parent2,mailing_address,email,phone_number,allergy,end_date,allow_picture,note FROM Student LIMIT 1";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();

        //create the sql statements to execute
        for($i = 0; $i < $count; $i++){
            //Initialize sql statements
            $sql1 = "UPDATE Student";
            $sql2 = "SET ";
            $sql3 = "WHERE";
           
            //get the id
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);

            //create change flag
            $change = false;

            //check if the row is updated at all and create the body of the sql update statement
            $firstpass = 1;
            foreach($finfo as $field){
                $val = "";
                $fieldname = "$field->name";
                $predata = getFieldValue($db,"Student",$field->name,$id);
                $preval = $predata['data'][0][$field->name];
                $newval = $_POST[$fieldname];

                //look for a change
                if(strpos($field->name,'ID') == false AND $preval != $newval){
                    $change = true;
                }

                //if change was found in the row
                if($change){
                    $condition = "";
                    if($firstpass != 1)
                        $condition .= ',';
                    else
                        $firstpass = 0;

                    //check if the val is: NULL,NUMERIC,STRING
                    if($newval == ""){
                        $condition .= "$field->name = NULL";
                    }elseif($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition .= "$field->name = $newval";
                    }else{
                        $condition .= "$field->name = \"$newval\"";
                    }
                    
                    $sql2 .= "$condition";
                }
            }

            //create sql3
            $sql3 = "WHERE studentID = $id";

            //combine and push combined sql to statement
            if($change){
                array_push($studentIDs,$id);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "student updated!";
                $studentData = getStudentByID($db,$id);
                showAdvancedData($studentData['data'],$studentData['fields'],"Student Information","/account/viewDetails.php");
            }else{
                echo "Error with sql statement: $sql <br>\n";
                echo "Error Discription: ".mysqli_error($db)."<br>\n";
            }

            $count++;
        }
        if($count == 0){
            echo "<h1>Nothing Changed</h1>\n";
        }

        $result->free();
        $db->close();
        ?>
</body>
</html> 
