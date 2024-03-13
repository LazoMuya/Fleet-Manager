<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="config\stylesheet.css">
        <title>LogIn</title>
    </head>
    <body><div class="content">
        <form name="login" action="login.php"  method="POST" onsubmit="return validateForm()"><div class="form__group field"></div>
            <h2>Log In</h2>
            <label>Employee Number</label><br>
            <input type="text" name="emp_no" placeholder="Enter Employee Number"><br><br>

            <label>Password</label><br>
            <input type="password" name="password" placeholder="Enter Password"><br><br>

            <button type="submit" class="button">Login</button> <br><br>
            <?php
                $mysqli = require __DIR__ . "\config\dbconnect.php";

                if ($_POST != NULL){
                    $emp_no = $_POST['emp_no'];

                    $query = sprintf("SELECT * FROM emp_table WHERE emp_number = '$emp_no' and active = '1'");
                    
                    $result = $mysqli->query($query);
                    $user = $result->fetch_assoc();
                    if ($user != NULL){
                        if($_POST['password'] == $user["password"]){
                            session_start();
                            $_SESSION["user"] = $user["name"];
                            $_SESSION["id"] = $user["emp_number"];
                            $_SESSION["role"] = $user["role"];
                            header("Location: redirect.php");
                        } else
                            echo "<div class='message'>Invalid Login</div>";
                    } else
                        echo "<div class='message'>Invalid Login</div>";
                }
            ?>
        </form></div>
    </body>
    <script>
        function validateForm() {
            var empno = document.forms.login.emp_no.value;
            var password = document.forms.login.password.value;
            if (empno == ""  || empno == null) {
                alert("Employee number must be filled out");
                return false;
            }
            if (password == "" || password == null) {
                alert("Password is required");
                return false;
            }
        }
    </script>
</html>

