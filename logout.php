<?php
    session_start();
    session_destroy();
    header("refresh:2; login.php");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Logging Off.....</title>
</head>
<body>
    <div class="content">
        <div class='message'>
            <p>Successsfully Logged out. Redirecting to login page.......</p>
        </div>
    </div>
</body>
</html>