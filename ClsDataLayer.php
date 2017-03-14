<?php
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/3/17
 * Time: 10:43 AM
 */

namespace clawrence;
use PDO;

/**
 * Class ClsDataLayer
 * @package clawrence
 *
 * this class contains all of the database interactions
 */
class ClsDataLayer
{
    private $inDBH;


    /**
     * ClsDataLayer constructor.
     */
    function __construct() {
        $this->dbConnect();
    }


    /**
     * this function establishes the connection object
     */
    private function dbConnect() {


        if ($this->inDBH === null) {
            try {

                $dbConfig = include('MySQLConfig.php');
                $user = $dbConfig['username'];
                $pass = $dbConfig['password'];
                $dsn = 'mysql:host=' . $dbConfig['host'] . ";dbname=" . $dbConfig['database'];

                $this->inDBH = new PDO($dsn, $user, $pass);

                $this->inDBH->setAttribute(PDO::ATTR_ERRMODE,
                                           PDO::ERRMODE_EXCEPTION);
                $this->inDBH->setAttribute(PDO::ATTR_AUTOCOMMIT, true);

            } catch (\PDOException $e) {
                print "Error!: Database Connection Unavailable" . "<br/>";
                die();
            }
        }
    }

    /**
     * this function will add or update employee data for a given employee
     * @param $inEmployeeId
     * @param $inFirstName
     * @param $inLastName
     * @param $inHourlyWage
     * @param $inExemptStatus
     */
    function addEditEmployee($inEmployeeId, $inFirstName, $inLastName, $inHourlyWage, $inExemptStatus) {

        $this->dbConnect();

        if ($inEmployeeId == 0) {

            $inputSql = "Insert Into Employees (employee_first_name, employee_last_name, hourly_wage, exempt_flag) 
	                VALUES (?, ?, ?, ?);";

            $sth = $this->inDBH->prepare($inputSql);
            $sth->execute(Array($inFirstName, $inLastName, $inHourlyWage, $inExemptStatus));
        }

        else {
            $inputSql = "Update Employees set employee_first_name = ?, employee_last_name = ?, hourly_wage = ?, 
	                                     exempt_flag = ? 
	                Where employee_id = ?";

            $sth = $this->inDBH->prepare($inputSql);
            $sth->execute(Array($inFirstName, $inLastName, $inHourlyWage, $inExemptStatus, $inEmployeeId));

        }

    }

    /**
     * this function gets all of the employee data from the database
     * @return mixed
     */
    function getEmployees(){

        $this->dbConnect();

        $inputSql = "select employee_id,employee_first_name, employee_last_name, hourly_wage, exempt_flag from Employees
	                order by employee_id";

        $sth = $this->inDBH->prepare($inputSql);
        $sth->execute();

        $result = $sth->fetchAll();

        return $result;
    }

    /**
     * this function checks for a matching value in the Users table
     * for the login and password provided, returning 1 if a match is found
     * @param $login_name
     * @param $login_pwd
     * @return int
     */
    function checkLogin($login_name, $login_pwd) {

        $this->dbConnect();

        $login_check = 0;

        $inputSql = "SELECT 1 FROM Users Where user_name = ? and user_pwd = ?";

        $sth = $this->inDBH->prepare($inputSql);

        $sth->execute(Array($login_name,$login_pwd));

        $login_check = $sth->fetchColumn();

        return $login_check;
    }

    /**
     * this function gets the payroll data set for a given week and year
     * @param $inWeek
     * @param $inYear
     * @return mixed
     */
    function getPayrollData($inWeek, $inYear) {

        $this->dbConnect();

        $inputSql = "select e.employee_id,e.employee_first_name, e.employee_last_name, coalesce(p.hours_worked,0) as hours_worked,
                    e.hourly_wage, e.exempt_flag
	                from Employees e
	                left join PayrollData p on e.employee_id = p.employee_id
					         and  p.week_number = ? and p.year = ?
	                order by e.employee_id";

        $sth = $this->inDBH->prepare($inputSql);

        $sth->execute(Array($inWeek,$inYear));

        $result = $sth->fetchAll();

        return $result;
    }

    /**
     * this function checks for the existence for payroll data for a given
     * week, year and employee id
     * @param $inWeek
     * @param $inYear
     * @param $inEmployeeId
     * @return mixed
     */
    function checkForPayrollData($inWeek, $inYear, $inEmployeeId) {

        $this->dbConnect();

        $inputSql = "select 1 from PayrollData
	                        where employee_id = ? and week_number = ? and year = ?";

        $sth = $this->inDBH->prepare($inputSql);

        $sth->execute(Array($inEmployeeId,$inWeek,$inYear));

        $result = $sth->fetchColumn();

        return $result;

    }

    /**
     * this function creates or updates hours worked payroll data for a given week/year/employee
     * depending on the update directive provided
     * @param $updateFlag
     * @param $week
     * @param $year
     * @param $employeeId
     * @param $hoursWorked
     */
    function insertUpdatePayrollData($updateFlag, $week, $year, $employeeId, $hoursWorked) {

        $this->dbConnect();

        if ($updateFlag == 0) {
            $inputSql = "insert into PayrollData (hours_worked, employee_id, week_number, year)
	                            values (?,?,?,?)";
        }
        else if ($updateFlag == 1) {
            $inputSql = "update PayrollData set hours_worked = ?
	                              where employee_id = ? and week_number = ? and year = ?";
        }

        $sth = $this->inDBH->prepare($inputSql);

        $sth->execute(Array($hoursWorked,$employeeId, $week,$year));


    }


}