<?php
/***********************************************************
 * PHP settings.
 **********************************************************/
ini_set('date.timezone', 'Asia/Tokyo');
ini_set('session.save_path', '../sess');
ini_set('session.save_handler', 'files');
ini_set('display_errors', 1);


/***********************************************************
 * Developers configurations.
 **********************************************************/
define("TMPDIR", "../var/");
define("IDENT", "maglo");
define("LANG", "ja");
define("SEND", "../var/sender_list");
define("CONFIG", "../etc/maglo.conf");
define("PASSFILE", "../etc/admin_passwd");
define("STATUS", "../var/status/");
define("SENDLOCK", "/sending.lock");
define("SENDTIME", "/sendtime");
define("SENDHOLD", "/sending_hold");
define("SENDLOG", "/sending_log");
define("SENDERR", "/sending_err");
define("MAILINFO", "/mailinfo");
define("INFORAW", "/mailinfo.raw");
define("STARTEND", "/start-end_time");
define("ENVELOPE", "/envelope_to_list");
define("SENDTEST", "/send_test");
define("SENDIMMD", "/send_immd");
define("TESTSEND", "../bin/msqualldeliver -t");
define("IMMDSEND", "../bin/msqualldeliver -i");


/***********************************************************
 * Error messages
 **********************************************************/
include_once("../inc/message.inc");

/***********************************************************
 * Application Configuratin rules.
 **********************************************************/
$confRules = array(
                    "SessionTimeout" => array("default"  => "1600",
                                              "errcode" => CONFIG_SESS,
                                              "method" => "gt",
                                              "addargs"  => "0"),
                    "LogFacility"    => array("default"  => "local1",
                                              "errcode" => CONFIG_LOG,
                                              "method" => "facility",
                                              "addargs"  => ""),
	    "SendMail"       => array("default"  => "/usr/sbin/sendmail",
                                              "errcode" => CONFIG_SENDMAIL,
                                              "method" => "fileExecutable",
                                              "addargs"  => ""),
                    "FromDomain"     => array("default"  => "",
                                              "errcode" => CONFIG_FROM,
                                              "method" => "required",
                                              "addargs"  => ""),
                    "AdminDir"       => array("default"  => "../var/",
                                              "errcode" => CONFIG_ADMIN,
                                              "method" => "dirExist",
                                              "addargs"  => "")
               );

/***********************************************************
 * Include Base library.
 **********************************************************/
include_once("../lib/libvalidation.php");
include_once("../lib/libexception.php");
include_once("../lib/libdisplay.php");
include_once("../lib/libconfig.php");
include_once("../lib/libsession.php");

?>
