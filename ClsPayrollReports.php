<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/6/17
 * Time: 8:55 AM
 */

namespace clawrence;
require_once 'ClsDataLayer.php';
require_once 'ClsUtility.php';

/**
 * Class ClsPayrollReports
 * @package clawrence
 * This class serves as the controller for the Payroll Reports page
 * The design uses class-level fields for the majority of the shared data
 * required between methods.
 */
class ClsPayrollReports
{
    private $cookieArray;
    private $postDataArray;
    private $cookieOk;
    private $cookieNotOkText;
    private $weeks;
    private $years;
    private $weekSelected;
    private $yearSelected;
    private $payrollDataCalculated;
    private $dataLayer;
    private $utility;

    /**
     * ClsPayrollReports constructor.
     * @param $cookieArray
     * @param $postDataArray
     */
    function __construct($cookieArray, $postDataArray)
    {
        $this->cookieArray = $cookieArray;
        $this->postDataArray = $postDataArray;
        $this->dataLayer = new ClsDataLayer();
        $this->utility = new ClsUtility();
    }

    /**
     * this function drives the business logic processing for the payroll reports page
     */
    function processPageData() {

        $this->cookieOk = $this->utility->checkAuthCookie($this->cookieArray);

        if ($this->cookieOk == 1) {

            $this->weeks = $this->utility->getWeeks();
            $this->years = $this->utility->getYears();

            $this->processPostData();

            $this->payrollDataCalculated = $this->generatePayrollDataCalculated();

        }
        else {
            $this->cookieNotOkText = $this->utility->getCookieNotOkText();
        }

    }

    // getters for class fields

    /**
     * @return mixed
     */
    function getWeeks() {
        return $this->weeks;
    }

    /**
     * @return mixed
     */
    function getYears() {
        return $this->years;
    }

    /**
     * @return mixed
     */
    function getPayrollDataCalculated() {
        return $this->payrollDataCalculated;
    }

    /**
     * @return mixed
     */
    function getCookieOk() {
        return $this->cookieOk;
    }

    /**
     * @return mixed
     */
    function getCookieNotOkText() {
        return $this->cookieNotOkText;
    }

    /**
     * @return mixed
     */
    function getWeekSelected() {
        return $this->weekSelected;
    }

    /**
     * @return mixed
     */
    function getYearSelected() {
        return $this->yearSelected;
    }

    // end getters

    /**
     * function that processes the data from the post array
     */
    private function processPostData() {

        if (isset($this->postDataArray['week']) && isset($this->postDataArray['year']))
        {

            $this->weekSelected = $this->utility->cleanse_input($this->postDataArray['week']);
            $this->yearSelected = $this->utility->cleanse_input($this->postDataArray['year']);
        }
        else
        {
            $this->weekSelected = $this->weeks[0];
            $this->yearSelected = $this->years[0];

        }

    }

    /**
     * this function resets the expiry on the auth cookie.  It checks the class variable
     * first to ensure validation has occurred.
     */
    function resetCookie() {
        if ($this->cookieOk == 1) {
            $this->utility->setCookie();
        }

    }

    /**
     * function that generates the calculated payroll data array
     * for the week and year posted to the page
     * @return array
     */
    private function generatePayrollDataCalculated() {

        $employeeCount = 0;
        $totalHoursWorked = 0.0;
        $totalBasePay = 0.0;
        $totalOvertimePay = 0.0;
        $totalGrossPay = 0.0;

        $payrollData = $this->dataLayer->getPayrollData($this->weekSelected, $this->yearSelected);

        $payrollDataCalculated= [];

        $index = 0;

        foreach ($payrollData as $payrolldatum) {

            $hoursWorked = $payrolldatum[3];
            $hourlyWage = $payrolldatum[4];
            $exemptFlag = $payrolldatum[5] == 0 ? 'N' : 'Y';

            $weeklyPay = $this->calculateWeeklyPay($hoursWorked, $hourlyWage, $exemptFlag);

            $payrollDataCalculated[$index] = array_merge($payrolldatum,$weeklyPay);

            $employeeCount += 1;
            $totalHoursWorked += $hoursWorked;
            $totalBasePay += $weeklyPay[0];
            $totalOvertimePay += $weeklyPay[1];
            $totalGrossPay += $weeklyPay[2];

            $index++;
        }

        $totalPayrollData = ["","TOTALS","",$totalHoursWorked,"","",$totalBasePay,$totalOvertimePay,$totalGrossPay];

        $averagePayrollData = ["", "AVERAGES", "", $totalHoursWorked/$employeeCount, "", "",
            $totalBasePay/$employeeCount,
            $totalOvertimePay/$employeeCount,
            $totalGrossPay/$employeeCount];

        array_push($payrollDataCalculated,$totalPayrollData, $averagePayrollData);

        return $payrollDataCalculated;
    }

    /**
     * function that contains the business logic for calculating weekly pay
     * based on hours/wage/exempt status
     * @param $hoursWorked
     * @param $hourlyWage
     * @param $exemptFlag
     * @return array
     */
    private function calculateWeeklyPay($hoursWorked, $hourlyWage, $exemptFlag) {

    $baseHours = 40.0;
    $otMultiplier = 1.5;

    $basePay = round(($hoursWorked > 40 ? $baseHours : $hoursWorked) * $hourlyWage, 2);

    $overtimePay = 0.0;

    if ($exemptFlag == 'N' and $hoursWorked > 40) {

        $overtimePay = round(($hoursWorked - $baseHours) * ($hourlyWage * $otMultiplier), 2);

    }

    $grossPay = ($basePay) + ($overtimePay);

    $weekPay = [$basePay,$overtimePay,$grossPay];

    return $weekPay;

    }
}