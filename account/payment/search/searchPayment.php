<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../../mystyle.css">
    <link rel="stylesheet" type="text/css" href="../../print.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Payment Search Results</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../../account.php">Account Info</a></span>
                <span ><a class="button" href="../payment.php">All Payments</a></span>
                <span ><a class="button" href="../search/searchPayment_page.php">Search Payment</a></span>
                <span ><a class="button" href="../../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>
        <p>
        </p>

    <?php
        //connect to database
        include("../../../config.php");
        $db = connect();
        checkSession();

        //get field values for payment table
        //names from POST are Table column names
        include("../queries.php");
        $sql = "SELECT 'payment id' as `paymentID`, 'date_start' as `date_start`,'date_end' as `date_end`,amount,description,method FROM Payment";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT paymentID,student_1,student_2,student_3,amount,date,time,description,fk_accountID FROM Account,Payment";
            $sql2 = "WHERE accountID= fk_accountID";
            $sql3 = "ORDER BY date DESC,time DESC";
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--" and $val != "::"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name $eq $val";
                    }elseif($field->name == 'date_start' OR $field->name == 'date_end'){
                        $condition = "date $eq '$val'";
                    }elseif($field->name == 'accountID'){
                        $condition = "fk_accountID $eq $val";
                    }else{
                        $condition = "$field->name $eq '$val'";
                    }

                    $sql2 .= " AND $condition";
                }
            }
            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            if($ORDERBY != ""){
                $sql3 = "ORDER BY $ORDERBY";
            }

            $result->free();
            $sql = "$sql1 $sql2 $sql3";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                $found = mysqli_num_rows($result);

                //get the data
                $finfo = $result->fetch_fields();
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }

                //show the results
                showAdvancedData($data,$finfo,'Search Results',"../update/search_update.php");
            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

            echo "<br><br>\n";

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }

        $result->free();
        $db->close();
    ?>



</body>
</html> 


