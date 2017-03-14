<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/6/17
 * Time: 8:39 PM
 */
require_once 'ClsAddEditEmployees.php';

register_shutdown_function('shutDownFunction');

$clsAddEditEmployees = new clawrence\ClsAddEditEmployees($_COOKIE, $_POST);

$clsAddEditEmployees->processPageData();

$cookieOk = $clsAddEditEmployees->getCookieOk();

if ($cookieOk == 0) {
    echo $clsAddEditEmployees->getCookieNotOkText();
    exit(0);
}

$employeeData = $clsAddEditEmployees->getEmployeeData();

$clsAddEditEmployees->resetCookie();

?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css">

<head>
    <title>Add or Edit Employee</title>
    <script type="text/javascript">//<![CDATA[

        function editrec(rowid) {

            var editEmployeeID = rowid;
            var editFirstName = document.getElementById(rowid).cells[1].innerHTML;
            var editLastName = document.getElementById(rowid).cells[2].innerHTML;
            var editHourlyWage = document.getElementById(rowid).cells[3].innerHTML;
            var editExemptFlag = document.getElementById(rowid).cells[4].innerHTML;

            document.getElementById("employeeid").value = editEmployeeID;
            document.getElementById("firstname").value =  editFirstName;
            document.getElementById("lastname").value =  editLastName;
            document.getElementById("hourlywage").value =  editHourlyWage;
            document.getElementById("exempt").checked =  (editExemptFlag == "Yes" ? true : false);
        }

        function validateform() {

            var validNamePattern = new RegExp(/^[A-z0-9 \.\-]{1,50}$/);
            var firstname = document.forms["addeditemployee"]["firstname"].value;
            var lastname = document.forms["addeditemployee"]["lastname"].value;
            var hourlywage = document.forms["addeditemployee"]["hourlywage"].value;

            if (!validNamePattern.test(firstname)) {
                alert("First Name must be between 1 and 50 characters and contain no special characters other than - or .");
                return false;
            }

            if (!validNamePattern.test(lastname)) {
                alert("Last Name must be between 1 and 50 characters and contain no special characters other than - or .");
                return false;
            }

            if (isNaN(hourlywage) || hourlywage <= 0) {
                alert("Hourly Wage must be a number greater than 0");
                return false;
            }

        }


        //]]></script>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<p>
    <a href="default.php">Return to Main Menu</a>
</p>
<h1>
    Add or Edit Employee
</h1>
<p>
    Please enter the following info for a new employee or choose an employee to edit:
</p>
<form method="post" action="addeditemployees.php" enctype="multipart/form-data" id="addeditemployee" onsubmit="return validateform();">
    <input type="hidden" name="employeeid" value="0" id="employeeid" /><b>
        First Name of Employee:
    </b>
    <input type="text" name="firstname"  size="50" id="firstname" /><br />
    <b>
        Last Name of Employee:
    </b>
    <input type="text" name="lastname"  size="50" id="lastname" /><br />
    <b>
        Hourly Wage
    </b>
    <input type="text" name="hourlywage"  size="10" id="hourlywage" /><br />
    <b>
        Employee is exempt from overtime pay
    </b>
    <label>
        <input type="checkbox" name="exempt" value="on" id="exempt" />exempt
    </label>
    <br />
    <input type="submit" name=".submit" value="Submit" /><input type="reset"  name=".reset" value="Reset" /><div><input type="hidden" name=".cgifields" value="exempt"  /></div></form><br />
<TABLE border='1' width='100%'><tr>
        <th>
            Edit
        </th>
        <th>
            First Name
        </th>
        <th>
            Last Name
        </th>
        <th>
            Wage
        </th>
        <th>
            Exempt
        </th>
    </tr>
    <?php foreach ($employeeData as $employeeDatum): ?>
    <tr id="<?=$employeeDatum[0];?>">
        <td><input type="button"  name="Edit" value="Edit" onclick="editrec(<?=$employeeDatum[0];?>)" /></td>
        <td><?=$employeeDatum[1];?></td> <td><?=$employeeDatum[2];?></td> <td><?=$employeeDatum[3];?></td>
        <td><?=$employeeDatum[4] == 1 ? "Yes" : "No";?></td>
    </tr>
    <?php endforeach; ?>
</TABLE>
</body>
</html>

<?php
function shutDownFunction() {
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if ($error['type'] === E_ERROR) {
        header("Location: /error.html");
    }
}
?>
