<html>
<head>
<link rel="stylesheet" type="text/css" href="/mystyle.css">
<script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<h1>Confirmation Page</h1>

        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="/homepage.php">Homepage</a></span>
                <span ><a class="button" href="/account/account.php">Account Info</a></span>
                <span ><a class="button" href="/logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>

        <?php
        //connect to database
        include("../../../config.php");
        include("../queries.php");
        $db = connect();

        //get post variables
        $count = (int)$_POST['count'];
        $oneID = $_POST['id'];
        $formIDs = array();
        for($i = 0; $i < $count; $i++){
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            array_push($formIDs,$id);
        }

        //execute delete query
        if($oneID != ""){
            $sql = "DELETE FROM Form WHERE formID = $oneID";
            $aid = $_POST['aid'];
            $accountData = getAccountByID($db,$aid);
            $formData = getFormByID($db,$oneID);
            showData2($accountData['data'],$accountData['fields']);
            showData($formData['data'],$formData['fields']);
            mysqli_query($db,$sql);
            if($result !== false)
                echo "<br>Successfully Deleted Record<br>\n";
            else
                echo "Could not delete the reord!<br>\n";
        }

        $result->free();
        $db->close();
        ?>
</body>
</html> 


