<html>
<head>
    <link rel="stylesheet" type="text/css" href="/mystyle.css">
    <script type='text/javascript' src="/js/js_main.js"></script>
</head>
<body>
<div class='title'>
<h1>Search Results</h1>
</div>
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
        <p>
        </p>

    <?php
        //connect to database
        include("../../../config.php");
        include("../queries.php");
        $db = connect();

        $aid = $_POST['id'];

        //query database for files regarding the account
        $data = searchForm($db,$aid);

        //get each file and display them nicely with a href link to the file location
        showDeletableForm($data['data'],$data['fields']);

        $db->close();
    ?>

</body>
</html> 


