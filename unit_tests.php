<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/9/17
 * Time: 6:51 PM
 *
 * run using:
 * php phpunit-6.0.8.phar unit_tests.php
 *
 * The first 4 tests verify that passing no cookie data results in the cookieOk variable as 0 and various
 * page class variables being null due to the cookie test failing.
 *
 * The last 3 tests verify the Utility and DataLayer classes.
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once 'ClsDataLayer.php';
require_once 'ClsUtility.php';
require_once 'ClsDefault.php';
require_once 'ClsAddEditEmployees.php';
require_once 'ClsPayrollEntry.php';
require_once 'ClsPayrollReports.php';
use clawrence\ClsDataLayer;
use clawrence\ClsUtility;
use clawrence\ClsDefault;
use clawrence\ClsAddEditEmployees;
use clawrence\ClsPayrollEntry;
use clawrence\ClsPayrollReports;

final class SiteTest extends TestCase
{

    /**
     * test function to verify when no cookie is present,
     * function returns 0
     */
    public function test_CookieAuth_ClsDefault(): void
    {

        $clsDefault = new ClsDefault($_COOKIE,$_POST);

        $clsDefault->processPageData();

        $cookieOk = $clsDefault->getCookieOk();

        $this->assertEquals(
            0,
            $cookieOk
        );
    }

    /**
     * test function to verify when no cookie is present,
     * page does not continue to process and populate the weeks
     * array
     */
    public function test_getweeksNull_ClsPayrollEntry(): void
    {

        $clsPayrollEntry = new ClsPayrollEntry($_COOKIE,$_POST);

        $clsPayrollEntry->processPageData();

        $weeks = $clsPayrollEntry->getWeeks();

        $this->assertEquals(
            null,
            $weeks
        );
    }

    /**
     * test function to verify when no cookie is present,
     * page does not continue to process and populate the years
     * array
     */
    public function test_getyearsNull_ClsPayrollReport(): void
    {

        $clsPayrollReports = new ClsPayrollReports($_COOKIE,$_POST);

        $clsPayrollReports->processPageData();

        $years = $clsPayrollReports->getYears();

        $this->assertEquals(
            null,
            $years
        );
    }

    /**
     * test function to verify when no cookie is present,
     * page does not continue to process and populate the employee data
     * array
     */
    public function test_getEmployeeDataNull_ClsAddEditEmployees(): void
    {

        $clsAddEditEmployees = new ClsAddEditEmployees($_COOKIE,$_POST);

        $clsAddEditEmployees->processPageData();

        $employeeData = $clsAddEditEmployees->getEmployeeData();

        $this->assertEquals(
            null,
            $employeeData
        );
    }

    /**
     * test function to verify the weeks range returned from the
     * utility function is correct and functional
     */
    public function test_weeks_get(): void
    {
        $utility = new ClsUtility();

        $result = $utility->getWeeks();

        $this->assertEquals(
            range(1,52),
            $result
        );
    }

    /**
     * test function to confirm that database connectivity
     * to the employee and payroll tables is working
     */
    public function test_payrolldata_get(): void
    {
        $dataLayer = new ClsDataLayer();

        $result = $dataLayer->getPayrollData(1, 1900);

        $testVar = $result[0][0];

        $this->assertEquals(
            1,
            $testVar
        );
    }

    /**
     * test function to confirm that test data exists in the database and
     * can be accessed
     */
    public function test_datalayer_access(): void
    {
        $dataLayer = new ClsDataLayer();
        $this->assertEquals(
            1,
            $dataLayer->checkForPayrollData(1,2016,1)
        );
    }

}