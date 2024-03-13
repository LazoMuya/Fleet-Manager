<!DOCTYPE html>
<html>
<head>
    <style>
        table {
    max-width: 100%;
    text-align: center;
    vertical-align: center;
    border-style: none;
    border-radius: 25px;
  }
  thead{
    background-color: rgba(255, 255, 255, 0.253);
  }
  td {
    padding: 4px;
    border-top: 1px solid #000000;
  }
  tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.253);
  }
  th {
    border-radius: 5px;
    padding-top: 4px;
    padding-bottom: 4px;
    padding-right: 12px;
    padding-left: 12px;
    background-color: #012842b0;
    text-shadow: 1px 1px 4px black;
    color: white;
  }
    </style>
    <title>Document</title>
</head>
<body>
    
<?php

$mysqli = require __DIR__ . "/dbconnect.php";
$sql = "SELECT name,emp_number,mobile,role FROM emp_table WHERE active = 1 ORDER BY role,emp_number";
$result = $mysqli->query($sql);
$mysqli->close();

$roles = array('manager', 'dispatch', 'engineering', 'driver');
$userArrays = array_fill(0, count($roles), array());

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $roleIndex = array_search($row["role"], $roles);
        $userArrays[$roleIndex][] = $row;
    }
}
?>
<div class="container">
<table>
    <tr>
        <th>No.</th>
        <th>Name</th>
        <th>Employee Number</th>
        <th>Mobile</th>
    </tr>
    <tbody>
        <?php foreach ($userArrays as $index => $users) { ?>
            <?php if (!empty($users)) { 
                $i = 1; ?>
                <tr>
                    <th colspan="4"><strong><?php echo ucfirst($roles[$index]); ?></strong></th>
                </tr>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?php echo $i++;?></td>
                        <td><?php echo $user["name"]; ?></td>
                        <td><?php echo $user["emp_number"]; ?></td>
                        <td><?php echo $user["mobile"]; ?></td>
                    </tr>
                <?php }
             } 
            }
        ?>
    </tbody>
</table>
</div>
<?php
?>
</body>
</html>
