<?php
    session_start();
    if (!isset($_SESSION["user"]))
        header("location: error.php");
    else if($_SESSION["role"] == "driver")
        header("location: home_driver.php");
    else if($_SESSION["role"] == "dispatch")
        header("location: home_dispatch.php");
    else if($_SESSION["role"] == "engineering")
        header("location: home_maintenance.php");
    else if($_SESSION["role"] == "manager")
        header("location: home_manager.php");
    //print_r($_SESSION);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Welcome</title>
</head>
<body>
    <div class="topnav"></div>
    <div class="content">
    <p>Welcome <?php print_r($_SESSION["user"]);?>, <?php print_r($_SESSION["role"]);?>. You should be redirected soon......</p>
    </div>
</body>
</html>