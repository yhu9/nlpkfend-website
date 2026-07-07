<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Student Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../student.php">Student Table</a></span>
                    <span ><a class="button" href="../add/addStudent_page.php">Add Student</a></span>
                    <span ><a class="button" href="../delete/deleteStudent_page.php">Delete Student</a></span>
                    <span ><a class="button" href="../update/updateStudent_page.php">Edit Student</a></span>
                    <span ><a class="button" href="../search/searchStudent_page.php">Search Student</a></span>
                    <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>
        <h1><b>Warning! This action cannot be taken back!</b></h1>
        <hr><br>

    <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkAdvancedSession(3);

        //get field values for student table
        //names from POST are Table column names
        $sql = "SELECT * FROM Student";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT * FROM Student";
            $sql2 = "WHERE";
            $sql3 = "ORDER BY status,last_name,first_name";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            $first_pass = true;
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if($field->name == "DOB" or strpos($field->name,'date') !== false){
                    $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                    if($tmp != '--'){
                        $date = DateTime::createFromFormat("m-d-Y",$tmp);
                        $val = $date->format('Y-m-d');
                    }else
                        $val = "";
                }else
                    $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name $eq $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name $eq '$val'";
                    }
                    //Check if its the first condition 
                    if($first_pass)
                        $sql2 .= " $condition";
                    else
                        $sql2 .= " AND $condition";
                    $first_pass = false;
                }
            }
            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            if($ORDERBY == ""){
                $sql3 = "ORDER BY status,last_name,first_name";
            }else{
                $sql3 = "ORDER BY $ORDERBY";
            }

            $result->free();
            if($sql2 == "WHERE")
                $sql = "SELECT * FROM Student $sql3";
            else
                $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                echo "<form action=\"execute_deleteStudent.php\" method=\"POST\">\n";
                $found = mysqli_num_rows($result);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
                $finfo = $result->fetch_fields();
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }
                //save post information
                $tmp = 0;
                foreach($data as $row){
                    $val = $row["studentID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showDeleteableStudent($db,$data,$finfo);
                echo "</form>\n";


            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

            echo "<br><br>\n";

        }else{
            echo "query: $sql <br>";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>

</body>
</html> 


