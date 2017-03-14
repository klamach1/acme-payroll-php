<?php
require_once 'ClsDataLayer.php';
require_once 'ClsUtility.php';
/**
 * Created by PhpStorm.
 * User: clawrence
 * Date: 3/4/17
 * Time: 1:57 PM
 */

register_shutdown_function('shutDownFunction');

$dataLayer = new clawrence\ClsDataLayer();
$utility = new clawrence\ClsUtility();
$authOK = 0;

if (isset($_POST['username']) && isset($_POST['password']))
{

    $username = $utility->cleanse_input($_POST['username']);
    $password = $utility->cleanse_input($_POST['password']);
    $authOK = $dataLayer->checkLogin($username,$password);
    if ($authOK == 1) {
        $utility->setCookie();
    }
}

?>
<?php if ($authOK == 1):  ?>

<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="1; url=/default.php">
    <script type="text/javascript">
        window.location.href = "/default.php"
    </script>
    <title>Page Redirection</title>
</head>
<body>

If you are not redirected automatically, follow this <a href='/default.php</a>.
        </body>
    </html>


<?php else: ?>
<html lang="en-US">
            <head>
                <title>Invalid Username or Bad Password</title>
            </head>
            <body>
                Invalid Username or Bad Password <br>
            </body>
        </html>
    <?php endif; ?>

<?php
function shutDownFunction() {
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if ($error['type'] === E_ERROR) {
        header("Location: /error.html");
    }
}
?>