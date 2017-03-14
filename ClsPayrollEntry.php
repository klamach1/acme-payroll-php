<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/6/17
 * Time: 7:30 PM
 */

namespace clawrence;
require_once 'ClsDataLayer.php';
require_once 'ClsUtility.php';

/**
 * Class ClsPayrollEntry
 * @package clawrence
 * This class serves as the controller for the Payroll Entry page
 * The design uses class-level fields for the majority of the shared data
 * required between methods.
 */
class ClsPayrollEntry
{
    private $cookieArray;
    private $postDataArray;
    private $cookieOk;
    private $cookieNotOkText;
    private $weeks;
    private $years;
    private $weekSelected;
    private $yearSelected;
    private $payrollData;
    private $dataLayer;
    private $utility;

    /**
     * ClsPayrollEntry constructor.
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
     * this function is the driver of the business logic processing for
     * the page
     */
    function processPageData() {

        $this->cookieOk = $this->utility->checkAuthCookie($this->cookieArray);

        if ($this->cookieOk == 1) {

            $this->weeks = $this->utility->getWeeks();
            $this->years = $this->utility->getYears();

            $this->processPostData();

            $this->payrollData = $this->dataLayer->getPayrollData($this->weekSelected, $this->yearSelected);

        }
        else {
            $this->cookieNotOkText = $this->utility->getCookieNotOkText();
        }

    }

    // class field getters

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
    function getPayrollData() {
        return $this->payrollData;
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

    //end of class field getters

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

        if (isset($this->postDataArray['updateenabled'])){
            if ($this->utility->cleanse_input($this->postDataArray['updateenabled']) == "true") {
                $this->processInsertUpdatePayrollData();
            }
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
     * this function contains the logic that processes the hours worked
     * post data for payroll
     */
    private function processInsertUpdatePayrollData() {

        foreach ($this->postDataArray as $postField => $postValue) {

            $fieldSplit = explode("_",$postField);

            if ($fieldSplit[0] == "hoursworked") {
                $postValue = $this->utility->cleanse_input($postValue);
                $employeeId = $fieldSplit[1];

                $updateFlag = 0;
                $updateFlag = $this->dataLayer->checkForPayrollData($this->weekSelected, $this->yearSelected,
                                                                     $employeeId);

                $this->dataLayer->insertUpdatePayrollData($updateFlag,$this->weekSelected, $this->yearSelected,
                                                          $employeeId,$postValue);
            }

        }
    }
}