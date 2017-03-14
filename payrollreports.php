<?php
require_once 'ClsPayrollReports.php';
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/4/17
 * Time: 9:18 PM
 */

register_shutdown_function('shutDownFunction');

$clsPayrollReports = new clawrence\ClsPayrollReports($_COOKIE,$_POST);

$clsPayrollReports->processPageData();

$cookieOk = $clsPayrollReports->getCookieOk();

if ($cookieOk == 0) {
    echo $clsPayrollReports->getCookieNotOkText();
    exit(0);
}

$weeks = $clsPayrollReports->getWeeks();
$years = $clsPayrollReports->getYears();

$weekSelected = $clsPayrollReports->getWeekSelected();
$yearSelected = $clsPayrollReports->getYearSelected();

$payrollDataCalculated = $clsPayrollReports->getPayrollDataCalculated();

$clsPayrollReports->resetCookie();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/readable/bootstrap.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<head>
    <title>Payroll Reports</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<p><a href="default.php">Return to Main Menu</a></p>
<form method="post" action="payrollreports.php" enctype="multipart/form-data"><h2>
        Payroll Report for </h2>
    <b>Week:</b>
    <select name="week" >

        <?php foreach ($weeks as $week): ?>
            <option <?=$weekSelected == $week ? 'selected="selected"' : '';?> value="<?=$week;?>"><?=$week;?></option>
        <?php endforeach; ?>

    </select><b> and Year: </b>

    <select name="year" >

        <?php foreach ($years as $year): ?>
            <option <?=$yearSelected == $year ? 'selected="selected"' : '';?> value="<?=$year;?>"><?=$year;?></option>
        <?php endforeach; ?>

    </select>
    <input type="submit" name=".submit" value="Run Different Report" />
</form><br />

<TABLE border='1' width='100%'><tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Hours</th>
        <th>Wage</th>
        <th>Exempt</th>
        <th>Base Pay</th>
        <th>Overtime Pay</th>
        <th>Gross Pay</th>
    </tr>

    <?php foreach ($payrollDataCalculated as $payrollDatumCalculated): ?>

    <tr id="<?=$payrollDatumCalculated[0];?>">
        <td><?=$payrollDatumCalculated[1];?></td>
        <td><?=$payrollDatumCalculated[2];?></td>
        <td><?=number_format($payrollDatumCalculated[3],2);?></td>
        <td><?=$payrollDatumCalculated[4] == '' ? '' : '$' . number_format($payrollDatumCalculated[4],2);?></td>
        <td><?=$payrollDatumCalculated[5] == 1 ? 'Yes' : 'No';?></td>
        <td><?='$' . number_format($payrollDatumCalculated[6],2);?></td>
        <td><?='$' . number_format($payrollDatumCalculated[7],2);?></td>
        <td><?='$' . number_format($payrollDatumCalculated[8],2);?></td>
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