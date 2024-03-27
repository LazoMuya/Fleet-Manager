<?php
    session_start();
    if($_SESSION["role"] != "manager"){
        header("Location: redirect.php");
        exit();
    }
    $mysqli = require __DIR__ . "\config\dbconnect.php";
    if ($_POST == NULL)
        $sql = "SELECT * FROM maintenance_table ORDER BY work_number DESC";
    else if ($_POST['month'] == 0){
        $year = $_POST['year'];
        $sql = "SELECT * FROM maintenance_table WHERE EXTRACT(YEAR FROM date) = '$year' ORDER BY work_number DESC";
    } else {
        $year = $_POST['year'];
        $month = $_POST['month'];
        $sql = "SELECT * FROM maintenance_table WHERE EXTRACT(MONTH FROM date) = '$month' AND EXTRACT(YEAR FROM date) = '$year' ORDER BY work_number DESC";
    }
    $result = $mysqli->query($sql);
    $mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="config\stylesheet.css">
    <title>Service Reports</title>
</head>
<body>
    <div class="topnav">
        <a href="home_manager.php">Home</a>
        <a href="vehicles.php">Vehicle Information</a>
        <a href="employees.php">Employee Information</a>
        <a href="deliveries.php">Deliveries Information</a>
        <a href="latedeliveries.php">Late Deliveries</a>
        <a href="fuelbreakdown.php">Fuel Information</a>
        <a class="active" href="servicereports.php">Service Reports</a>
        <div class="topnav-right">
            <a><?php echo $_SESSION['user'];?></a>
            <a href="logout.php">Log Out</a>
        </div>
    </div>
    <div class="inline">
    <form name="date" action="servicereports.php" method="POST">
        <h2>Select a specific time period</h2s>
        <select name="year" id="year" class="select">
            <option value="0" selected>Select Year to view</option>       
                <script>
                    let dateDropdown = document.getElementById('year');
                    let currentYear = new Date().getFullYear();    
                    let earliestYear = 2018;     
                    while (currentYear >= earliestYear) {      
                        let dateOption = document.createElement('option');          
                        dateOption.text = currentYear;      
                        dateOption.value = currentYear;        
                        dateDropdown.add(dateOption);      
                        currentYear -= 1;    
                    }
                </script>
        </select>

        <select name="month" id="month" class="select">
            <option value="0" default>Select Month to view (Optional)</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>

        <button type="submit" class="button">View report</button>
    </form>
    </div>
    <div class="container">
    <table>
        <tr >
            <th colspan="8"><h1>Service Operations Report</h1></th>
        </tr>
        <tr>
            <th>Work No.</th>
            <th>Vehicle</th>
            <th>Operations Done</th>
            <th>Next Service</th>
            <th>Date</th>
            <th>Durartion Taken</th>
            <th>Comment</th>
            <th>Carried Out By</th>
        </tr>
        <?php
            while($rows=$result->fetch_assoc())
            {
        ?>
        <tr>
            <td><?php echo $rows['work_number'];                                  ?></td>
            <td><?php echo $rows['vehicle_regno'];                                ?></td>
            <td><?php echo $rows['operations_done'];                              ?></td>
            <td><?php echo number_format($rows['next_service'])." Kms";           ?></td>
            <td><?php echo date_format(date_create($rows['date']),"D, d M Y");    ?></td>
            <td><?php echo $rows['duartion_taken'];                               ?></td>
            <td><?php if ($rows['comment'] != NULL) echo date_format(date_create($rows['comment']),"D, d M Y");   else echo "-";  ?></td>
            <td><?php echo $rows['emp_no'];                                       ?></td>
        </tr>
        <?php
            }
        ?>
        <tr><td colspan = "8"> End of report</td></tr>
    </table>
    </div>
</body>
</html>