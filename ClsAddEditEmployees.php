<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/6/17
 * Time: 8:39 PM
 */

namespace clawrence;
require_once 'ClsDataLayer.php';
require_once 'ClsUtility.php';

/**
 * Class ClsAddEditEmployees
 * @package clawrence
 * This class serves as the controller for the Add/Edit Employees page
 * The design uses class-level fields for the majority of the shared data
 * required between methods.
 */
class ClsAddEditEmployees
{
    private $cookieArray;
    private $postDataArray;
    private $cookieOk;
    private $employeeData;
    private $dataLayer;
    private $utility;

    /**
     * ClsAddEditEmployees constructor.
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

            $this->processPostData();

            $this->employeeData = $this->dataLayer->getEmployees();

        }
        else {
            $this->cookieNotOkText = $this->utility->getCookieNotOkText();
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

    // class field getters

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
    function getEmployeeData() {
        return $this->employeeData;
    }

    // end class field getters

    /**
     * this function processes post data if any exists
     */
    private function processPostData() {

        if (isset($this->postDataArray['employeeid'])) {

            $employeeId = $this->utility->cleanse_input($this->postDataArray['employeeid']);
            $firstName = $this->utility->cleanse_input($this->postDataArray['firstname']);
            $lastName = $this->utility->cleanse_input($this->postDataArray['lastname']);
            $hourlyWage = $this->utility->cleanse_input($this->postDataArray['hourlywage']);

            if (isset($this->postDataArray['exempt'])) {
                $exemptStatus = 1;
            } else {
                $exemptStatus = 0;
            }

            $this->dataLayer->addEditEmployee($employeeId, $firstName, $lastName, $hourlyWage, $exemptStatus);
        }

    }


}