<?php
include_once("../lib/initialize.php");
include_once("../lib/libstatus.php");
include_once("../lib/libmsqualldeliver.php");
include_once("../lib/dglibescape.php");


define("TITLE", "配信ステータス確認画面");
define("STATUS_1", "完了");
define("STATUS_2", "未配信");
define("STATUS_3", "配信中");
define("STATUS_4", "一時中断");
define("STATUS_5", "エラー");

$data = NULL;
$link = NULL;
$link_content = NULL;
$disp_flg = 1;
$disp_mess = "";
$search_s_time = "";
$search_e_time = "";

$status_rule =
     array(
         array("id"    => "search_s_time",
               "label" => "",
               "rule"  => array("dateFormat_non_empty" => INVALID_DELIVERY_DATE)
               ),
         array("id"    => "search_e_time",
               "label" => "",
               "rule"  => array("dateFormat_non_empty" => INVALID_DELIVERY_DATE)
               )
          );

try {
    new Config();

    /* Check the session */
    $sessObj = new sessionExecutive();
    $sessObj->startSess();
    $sessObj->checkSess();

    if (isset($_POST["search_s_time"]) === TRUE) {
        $search_s_time = $_POST["search_s_time"];
    }
    if (isset($_POST["search_e_time"]) === TRUE) {
        $search_e_time = $_POST["search_e_time"];
    }

    /* The main process of screen */
    /* Search button has been pressed */
    if (isset($_POST["search"]) === TRUE) {

        /* Check the search date */
        $statusObj = new formValidation();
        $statusObj->exec($status_rule);

        /* Check the difference of time */
        $checkObj = new status();
        $checkObj->checkDifftime($search_s_time, $search_e_time);
        $data = $checkObj->readingStatus($search_s_time, $search_e_time);
        if ($data === FALSE) {
            $disp_mess = $msg[LANG][NO_SUCH_JOB]["web"];
            $data = NULL;
        }

    }

    /* Break button has been pressed */
    if (isset($_POST["break"]) === TRUE) {
        if (isset($_POST["check"]) === FALSE) {
            $disp_mess = $msg[LANG][NOT_SELECT_JOB]["web"];
        } else {
            $clickObj = new alterationJob();
            $ret = $clickObj->clickBreak($_POST["check"]);
            if ($ret === FALSE) {
                $disp_mess = $msg[LANG][CAN_NOT_BREAK]["web"];
                $logObj = new SystemWarn(CAN_NOT_BREAK);
                $logObj->resultlog();
            } else if ($ret === -2) {
                $disp_mess = $msg[LANG][ALREADY_BREAK]["web"];
                $logObj = new SystemWarn(ALREADY_BREAK);
                $logObj->resultlog();
            } else {
                $disp_mess = $msg[LANG][SUCCESS_BREAK]["web"];
                $logObj = new SystemWarn(SUCCESS_BREAK);
                $logObj->resultlog();
            }
        }
        $checkObj = new status();
        $checkObj->checkDifftime($search_s_time, $search_e_time);
        $data = $checkObj->readingStatus($search_s_time, $search_e_time);
        if ($data === FALSE) {
            $data = NULL;
        }
    }

    /* Restart button has been pressed */
    if (isset($_POST["restart"]) === TRUE) {
        if (isset($_POST["check"]) === FALSE) {
            $disp_mess = $msg[LANG][NOT_SELECT_JOB]["web"];
        } else {
            $clickObj = new alterationJob();
            $ret = $clickObj->clickRestart($_POST["check"]);
            if ($ret === FALSE) {
                $disp_mess = $msg[LANG][NOT_RESTART]["web"];
                $logObj = new SystemWarn(NOT_RESTART);
                $logObj->resultlog();
            } else {
                $disp_mess = $msg[LANG][SUCCESS_RESTART]["web"];
                $logObj = new SystemWarn(SUCCESS_RESTART);
                $logObj->resultlog();
            }
        }
        $checkObj = new status();
        $checkObj->checkDifftime($search_s_time, $search_e_time);
        $data = $checkObj->readingStatus($search_s_time, $search_e_time);
        if ($data === FALSE) {
            $data = NULL;
        }
    }

    /* Delete button has been pressed */
    if (isset($_POST["delete"]) === TRUE) {
        if (isset($_POST["check"]) === FALSE) {
            $disp_mess = $msg[LANG][NOT_SELECT_JOB]["web"];
        } else {
            $clickObj = new alterationJob();
            $ret = $clickObj->clickDelete($_POST["check"]);
            if ($ret === FALSE) {
                $disp_mess = $msg[LANG][NOT_DELETE]["web"];
                $logObj = new SystemWarn(NOT_DELETE);
                $logObj->resultlog();
            } else {
                $disp_mess = $msg[LANG][SUCCESS_DELETE]["web"];
                $logObj = new SystemWarn(SUCCESS_DELETE);
                $logObj->resultlog();
            }
        }
        $checkObj = new status();
        $checkObj->checkDifftime($search_s_time, $search_e_time);
        $data = $checkObj->readingStatus($search_s_time, $search_e_time);
        if ($data === FALSE) {
            $data = NULL;
        }

    }

    /* Link button has been pressed */
    if (isset($_GET["id"]) === TRUE && isset($_GET["page"]) === TRUE) {

        $linkObj = new dispLink();
        $link = $linkObj->contentConfirm($_GET["id"], $_GET["page"], $link_content);
        $data = NULL;
        $pageObj = new Display();
        $pageObj->tags["title"] = "";
        $pageObj->tags["message"] = $disp_mess;
    }

} catch (SystemWarn $warn) {

    /* Error handling */
    $disp_mess = $warn->makemessage();
    $warn->resultlog();
    if ($data !== NULL) {
        $checkObj = new status();
        $checkObj->checkDifftime($search_s_time, $search_e_time);
        $data = $checkObj->readingStatus($search_s_time, $search_e_time);
    }

}

try {

    $pageObj = new Display();
    $pageObj->tags["title"] = TITLE;
    $pageObj->tags["message"] = $disp_mess;

    if (isset($_POST["search_s_time"]) === TRUE) {
        $pageObj->tags["search_s_time"] = escape_html($_POST["search_s_time"]);
    } else {
        $pageObj->tags["search_s_time"] = "";
    }
    if (isset($_POST["search_e_time"]) === TRUE) {
        $pageObj->tags["search_e_time"] = escape_html($_POST["search_e_time"]);
    } else {
        $pageObj->tags["search_e_time"] = "";
    }

    $pageObj->tags["data"] = $data;
    $pageObj->tags["link"] = $link;
    $pageObj->tags["link_content"] = $link_content;


    if (isset($_GET["id"]) === TRUE && isset($_GET["page"]) === TRUE) {
        if (isset($link_content) === TRUE) {
            $pageObj->tags["from_addr"] = escape_html($link_content["from_addr"]);
            $pageObj->tags["to_addr"] = escape_html($link_content["to_addr"]);
            if (isset($link_content["reply_addr"]) === TRUE) {
               $pageObj->tags["reply_addr"] = escape_html($link_content["reply_addr"]);
            } else {
               $pageObj->tags["reply_addr"] = "";
            }
            $pageObj->tags["subject"] = escape_html($link_content["subject"]);
            $pageObj->tags["text"] = $link_content["text"];
        }

        if (isset($link) === TRUE) {
            $pageObj->tags["data"] = $link;
        }
    }

    $ret = $pageObj->view();

} catch (SystemCrit $crit) {
    /* System error screen display */
    $disp_msg = $crit->makemessage();
    $crit->resultlog();

    /* Display System error page */
    $pageObj->tags["message"] = $disp_msg;
    $pageObj->viewErr($disp_msg);
}

?>
