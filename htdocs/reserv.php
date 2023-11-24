<?php
/*************************************************************************
 * reserv.php
 *************************************************************************/
include_once("../lib/initialize.php");
include_once("../lib/libsender.php");
include_once("../lib/libreserv.php");

/* Initialize vars */
$disp_msg = "";
$sender_list = array();
$FT_addr = array();


/*************************************************************************
 * Page Setting
 *************************************************************************/
define("TITLE", "配信予約画面");

$commonrule = array(
                array("id"    => "fromaddr",
                      "label" => "Fromアドレス",
                      "rule"  => array("required" => NOT_SELECT_FROMADDR),
                ),
                array("id"    => "toaddr",
                      "label" => "Toアドレス",
                      "rule"  => array("required" => NOT_SELECT_TOADDR),
                ),
                array("id"    => "replyaddr",
                      "label" => "Reply-Toアドレス",
                      "rule"  => array("noreq"   => "",
                                       "email"   => INVALID_MADDR,
                                       "ge[5]"   => INVALID_LENGTH,
                                       "le[256]" => INVALID_LENGTH
                                 ),
                ),
                array("id"    => "subject",
                      "label" => "件名",
                      "rule"  => array("required"    => NO_INPUT,
                                       "OneByteKana" => INVALID_SUBJECT,
                                       "ge[1]"       => INVALID_LENGTH,
                                       "le[256]"     => INVALID_LENGTH
                                 ),
                ),
                array("id"    => "maintext",
                      "label" => "メール本文",
                      "rule"  => array("required"    => NO_INPUT,
                                       "OneByteKana" => INVALID_MAINTEXT,
                                       "ge[1]"       => INVALID_LENGTH,
                                       "le[102400]"  => INVALID_LENGTH
                                 ),
                ),
            );

$testrule = array(
                array("id"    => "testaddr",
                      "label" => "テスト配信先",
                      "rule"  => array("email"    => INVALID_MADDR,
                                       "ge[5]"    => INVALID_LENGTH,
                                       "le[256]"  => INVALID_LENGTH
                                 ),
                ),
            );

$reservrule = array(
                array("id"    => "date",
                      "label" => "配信予約日時",
                      "rule"  => array("required"   => NO_INPUT,
                                       "dateFormat" => INVALID_RESERV_DATE,
                                       "dateValue"  => PAST_RESERV_DATE
                                 ),
                ),
            );


/*************************************************************************
 * main
 *************************************************************************/

try {
    /* Read config */
    new Config();

    /* Make Display page instance */
    $pageObj = new Display();
    $pageObj->tags["message"] = "";

    /* Session check */
    $sessObj = new sessionExecutive();
    $sessObj->startSess();
    $sessObj->checkSess();

    /* Read sender and recipient list */
    read_sender_list($sender_list, SEND);

    /* Preparation of the select box */
    foreach ($sender_list as $line_num => $hash_value) {
        $hash = key($sender_list[$line_num]);
        $name = key($sender_list[$line_num][$hash]);
        $addr = $sender_list[$line_num][$hash][$name];

        if ($name === "" || $name === NULL) {
            $FT_addr["$line_num:$hash"] = $addr;
        } else {
            $FT_addr["$line_num:$hash"] = $name . "&lt;" . $addr . "&gt;";
        }

        if (isset($_POST["fromaddr"]) && $_POST["fromaddr"] === "$line_num:$hash") {
            $fname = htmlspecialchars_decode($name);
            $faddr = htmlspecialchars_decode($addr);
        }

        if (isset($_POST["toaddr"]) && $_POST["toaddr"] === "$line_num:$hash") {
            $tname = htmlspecialchars_decode($name);
            $taddr = htmlspecialchars_decode($addr);
        }
    }

    /* Processing of test or immediate or reservation delivery */
    if (isset($_POST["test_send"]) || isset($_POST["immd_send"]) || isset($_POST["rsrv_send"])) {

        /* Make formValidation instance */
        $validate = new formValidation();

        /* Validate input data (fromaddr, toaddr, replyaddr, subject, maintext) */
        $validate->exec($commonrule);

        /* Validate input data (testaddr) */
        if (isset($_POST["test_send"])) {
            $validate->exec($testrule);

        /* Validate input data (date) */
        } else if (isset($_POST["rsrv_send"])) {
            $validate->exec($reservrule);
        }

        if (isset($_POST["test_send"])) { 
            $reservObj = new prepTestdeliver();
        } else {
            $reservObj = new reserv();
        }

        $reservObj->tname = $tname;
        $reservObj->taddr = $taddr;

        $reservObj->fname = $fname;
        $reservObj->faddr = $faddr;

        /* Make job directory */
        $controlnum = $reservObj->makeControlnum();
        $reservObj->control_num = $controlnum;

        /* Make mailinfo file */
        $reservObj->makeMailinfo();

        /* Make sendtime file */
        $reservObj->makeSendtime();

        /* Make envelope_to_list.bz2 file */
        $reservObj->makeEnvelope_file();

        /* Make send_test or send_immd file */
        if (isset($_POST["test_send"]) || isset($_POST["immd_send"])) {
            $reservObj->makeFlagfile();

            if (isset($_POST["test_send"])) {
                /* Deliver mailmagazine */
                $ret = exec(TESTSEND);

                if ($ret === "") {
                    $disp_msg = $msg[LANG][TEST_DELIV_SUCCESS]["web"];
                } else {
                    $disp_msg = $msg[LANG][SEND_TEST_FAIL]["web"];
                }

                /* Delete job directory */
                $jobdir = STATUS . $controlnum;
                if(is_dir($jobdir) === FALSE) {
                    throw new SystemWarn(NO_SUCH_DIR, $jobdir);
                }

                $ret = exec("rm -rf $jobdir");
                if ($ret !== "") {
                    throw new SystemWarn(FAIL_DELETE_DIR, $jobdir);
                }

            } else if (isset($_POST["immd_send"])) {
                /* Deliver mailmagazine */
                $ret = exec(IMMDSEND);

                if ($ret === "") {
                    $disp_msg = $msg[LANG][IMMD_DELIV_SUCCESS]["web"];
                } else {
                    $disp_msg = $msg[LANG][SEND_IMMEDIATE_FAIL]["web"];
                }
            }
        }

        if (isset($_POST["rsrv_send"])) {
            $disp_msg = $msg[LANG][RESERV_SPECIFIED_SUCCESS]["web"];
        }
    }


} catch (SystemWarn $warn) {

    $disp_msg = $warn->makemessage();
    $warn->resultlog();
}

try {
    /* Make Display page instance */
    $pageObj = new Display();
    $pageObj->tags["message"] = $disp_msg;

    /* Get the current date */
    $current_time = time();
    $current_date = date("Y/m/d H:i", $current_time);

    /* Template tags */
    $pageObj->tags["title"] = TITLE;
    $pageObj->tags["FT_addr"] = $FT_addr;
    $pageObj->tags["date"] = $current_date;

    /* Display page */
    $ret = $pageObj->view();

} catch (SystemCrit $crit) {
    $disp_msg = $crit->makemessage();
    $crit->resultlog();

    /* Display System error page */
    $pageObj->tags["message"] = $disp_msg;
    $pageObj->viewErr($disp_msg);
}

?>
