<?php
/*************************************************************************
 * sender.php
 *************************************************************************/
include_once("../lib/initialize.php");
include_once("../lib/libsender.php");

/* Initialize vars */
$dispmsg = "";
$sender_list = array();


/*************************************************************************
 * Page Setting
 *************************************************************************/
define("TITLE", "送受信者管理");

$addrule = array(
            array("id"    => "add_name",
                  "label" => "追加識別名",
                  "rule"  => array("noreq"    => "",
                                   "ge[1]"    => INVALID_LENGTH,
                                   "le[64]"   => INVALID_LENGTH
                             ),
            ),
            array("id"    => "add_addr",
                  "label" => "追加メールアドレス",
                  "rule"  => array("required" => NO_INPUT,
                                   "email"    => INVALID_MADDR,
                                   "ge[5]"    => INVALID_LENGTH,
                                   "le[256]"  => INVALID_LENGTH
                             ),
            ),
        );

$modrule = array(
            array("id"    => "line_num",
                  "label" => "行番号",
                  "rule"  => array("required" => NOT_EXIST_SENDER),
            ),
            array("id"    => "hash",
                  "label" => "ハッシュ値",
                  "rule"  => array("required" => NOT_EXIST_SENDER),
            ),
            array("id"    => "name",
                  "label" => "識別名",
                  "rule"  => array("noreq"    => "",
                                   "ge[1]"    => INVALID_LENGTH,
                                   "le[64]"   => INVALID_LENGTH
                             ),
            ),
            array("id"    => "addr",
                  "label" => "メールアドレス",
                  "rule"  => array("required" => NO_INPUT,
                                   "email"    => INVALID_MADDR,
                                   "ge[5]"    => INVALID_LENGTH,
                                   "le[256]"  => INVALID_LENGTH
                             ),
            ),
        );

$delrule = array(
            array("id"    => "line_num",
                  "label" => "行番号",
                  "rule"  => array("required" => NOT_EXIST_SENDER),
            ),
            array("id"    => "hash",
                  "label" => "ハッシュ値",
                  "rule"  => array("required" => NOT_EXIST_SENDER),
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

    /* Make formValidation instance */
    $validate = new formValidation();

    /* Add */
    if (isset($_POST["add"])) {
        $validate->exec($addrule);

        alter_sender_list(SEND, 0, $_POST["add_line_num"], $_POST["add_hash"], $_POST["add_name"], $_POST["add_addr"]);
        $dispmsg = $msg[LANG][ADD_SUCCESS]["web"];

    /* Modify */
    } else if (isset($_POST["modify"])) {
        $validate->exec($modrule);

        alter_sender_list(SEND, 1, $_POST["line_num"], $_POST["hash"], $_POST["name"], $_POST["addr"]);
        $dispmsg = $msg[LANG][MODIFY_SUCCESS]["web"];

    /* Delete */
    } else if (isset($_POST["delete"])) {
        $validate->exec($delrule);

        alter_sender_list(SEND, 2, $_POST["line_num"], $_POST["hash"], $_POST["name"], $_POST["addr"]);
        $dispmsg = $msg[LANG][DELETE_SUCCESS]["web"];
    }

} catch (SystemWarn $warn) {
    $dispmsg = $warn->makemessage();
    $warn->resultlog();
    $pageObj->tags["message"] = $dispmsg;
    $pageObj->tags["failure"] = "";
}

try {
    /* Read sender and recipient list */
    read_sender_list($sender_list, SEND);

} catch (SystemWarn $warn) {
    $dispmsg = $warn->makemessage();
    $warn->resultlog();
}

try {
    /* Make Display page instance */
    $pageObj = new Display();
    $pageObj->tags["message"] = $dispmsg;

    /* Template tags */
    $pageObj->tags["title"] = TITLE;
    $pageObj->tags["sender"] = $sender_list;

    /* Display page */
    $ret = $pageObj->view();

} catch (SystemCrit $crit) {
    $dispmsg = $crit->makemessage();
    $crit->resultlog();

    /* Display System error page */
    $pageObj->tags["message"] = $dispmsg;
    $pageObj->viewErr($dispmsg);
}

?>
