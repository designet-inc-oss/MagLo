<?php
include_once("../lib/initialize.php");

define("TITLE", "ログイン画面");

/* Difine of use login */
define("ID_MIN",   "4");
define("ID_MAX",   "16");
define("PASS_MIN", "8");
define("PASS_MAX", "16");
define("NUM",      "0123456789");
define("SL",       "abcdefghijklmnopqrstuvwxyz");
define("LL",       "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("SYM_ID",   "-");
define("SYM_PASS", "!#$%&'()*+-./;<=>?@[\]^_`{|}~");

/* Variable initialization */ 
global $msg;
global $conf;
$rule = array();
$disp_mess = "";
$id_string = NUM . SL . SYM_ID;
$pass_string = NUM . SL . LL . SYM_PASS;
#$chk_login = 1;


/* Create a confirmation rule */
$rule = array(
    array("id"    => "id",
          "label" => "",
          "rule"  => array("required" => INVALID_ID),
    ),
    array("id"    => "passwd",
          "label" => "",
          "rule"  => array("required" => INVALID_ID),
    ),
);

try {

    /* Read configuration file. */
    new Config();

    /* Login button has been pressed */
    if (isset($_POST["submit"]) === TRUE) {

        /* Check number of characters of the ID */
        $loginObj = new formValidation();
        $loginObj->exec($rule);

        /* Convert SHA password */
        $en_pass = sha1($_POST["passwd"]);

        /* Start of session */
        $sessObj = new sessionExecutive();
        $sessObj->login = 1;
        $sessObj->startSess($_POST["id"], $en_pass);
        $sessObj->checkSess();

        /* Screen transition */
        header("Location: reserv.php");
        exit(0);
    
    }

    /* When the log out button is pressed */
    if (isset($_POST["logout"])) {
        throw new SystemWarn(LOGOUT);
    }
    
    $sessObj = new sessionExecutive();
    $sessObj->startSess();
    /* Start of session */
    if (isset($_COOKIE["direct"])) {
        if (isset($_SESSION["user"]) || $_COOKIE["direct"] == 1) {
            setcookie("direct", "", time() - 1800);
            $sessObj->login = 2;
            $sessObj->checkSess();
        }
    }

} catch (SystemWarn $warn) {
    
    /* warn error */
    $disp_mess = $warn->makemessage();
    $warn->resultlog();
    $sessObj = new sessionExecutive();
    $sess_id = session_id();
    if (isset($sess_id) === FALSE || isset($_POST["logout"]) === TRUE) {
        $sessObj->startSess();
        $sess_id = session_id();
    }
    if ($sess_id !== "") {
        $sessObj->destroySess();
    }

}

/* Screen display */
$pageObj = new Display();
try {
    $pageObj->tags["title"] = TITLE;
    $pageObj->tags["message"] = $disp_mess;
    $ret = $pageObj->view();
} catch (SystemCrit $crit) {
    /* critical error */
    $crit->resultlog();
    $pageObj->viewErr($disp_mess);
}

?>
