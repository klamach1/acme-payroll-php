<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/6/17
 * Time: 9:16 PM
 */

namespace clawrence;


/**
 * Class ClsUtility
 * @package clawrence
 *
 * This class serves as a utility class for common functions & variables that have no other home
 */
class ClsUtility
{
    private $cookieOk;
    private $cookieNotOkText = '<html lang="en-US">
            <head>
                <title>Invalid Username or Bad Password</title>
            </head>
            <body>
    Invalid Username or Bad Password
    </body>
        </html>';

    private $cookieName = 'myAuthCookie';

    private $cookieValue = 'UserIsOk';

    private $cookieTimeOut = 3600;

    private $weeks;

    private $years;

    /**
     * ClsUtility constructor.
     */
    function __construct() {
        $this->weeks = range(1,52);
        $this->years = range(2016,2017);
    }

    /**
     * this function will take the cookie array and validate the cookie
     * @param $cookieArray
     * @return int
     */
    function checkAuthCookie($cookieArray) {

        $this->cookieOk = 0;

        if (isset($cookieArray[$this->cookieName])) {
            if ($cookieArray[$this->cookieName] == $this->cookieValue)  {
                $this->cookieOk = 1;

            }
        }

        return $this->cookieOk;

    }

    /**
     * this function sets the user cookie using the class field settings
     */
    function setCookie() {
        setcookie($this->cookieName, $this->cookieValue,time()+$this->cookieTimeOut);
    }

    /**
     * this function sets the user cookie using the class field settings
     */
    function deleteCookie() {
        setcookie($this->cookieName, '',time()-3600);
    }

    // class level field getters
    /**
     * @return array
     */
    function getWeeks() {
        return $this->weeks;
    }

    /**
     * @return array
     */
    function getYears() {
        return $this->years;
    }

    /**
     * @return string
     */
    function getCookieNotOkText() {
        return $this->cookieNotOkText;
    }

    // end class level field getters

    //
    function cleanse_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}