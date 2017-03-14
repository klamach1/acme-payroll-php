<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/6/17
 * Time: 8:39 AM
 */

namespace clawrence;
require_once 'ClsDataLayer.php';
require_once 'ClsUtility.php';

/**
 * Class ClsDefault
 * @package clawrence
 * This class serves as the controller for the menu page
 * The design uses class-level fields for the majority of the shared data
 * required between methods.
 */
class ClsDefault
{
    private $cookieArray;
    private $postDataArray;
    private $cookieOk;
    private $cookieNotOkText;
    private $weeks;
    private $years;
    private $utility;

    /**
     * ClsDefault constructor.
     * @param $cookieArray
     * @param $postDataArray
     */
    function __construct($cookieArray, $postDataArray)
    {
        $this->cookieArray = $cookieArray;
        $this->postDataArray = $postDataArray;

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
    function getCookieOk() {
        return $this->cookieOk;
    }

    /**
     * @return mixed
     */
    function getCookieNotOkText() {
        return $this->cookieNotOkText;
    }

    // end class field getters

}