<?php
/***************************************************************************
 * CLASS NAME   ：SystemWarn
 * USE          ：Processing of exception that can be screen display
 * PROPERTY     ：$conf - The value of the configuration file
 *                $msg  - The value of message.inc
 **************************************************************************/
class SystemWarn extends Exception {
    public $args = array();
    public $code = "";

    public function __construct($code = 0, $args = array(), $message = null, Exception $previous = null)
    {
        $this->code = $code;
        $this->args = $args;
        parent::__construct($message, $code, $previous);
    }

    /**********************************************************************
     * METHOD NAME：makemessage()
     * USE        ：Creating an output message
     * ARGUMENT   ：none
     * RETURN     ：$ret - message
     **********************************************************************/
    public function makemessage()
    {
        global $msg;

        /* Return FALSE if there is no message code */
        if (!isset($msg[LANG][$this->code])) {
            return "";
        }

        /* Shaped into a error message */
        $ret = vsprintf($msg[LANG][$this->code]["web"], $this->args);

        /* Returns a message that is formatted */
        return $ret;
    }

    /**********************************************************************
     * METHOD NAME：resultlog()
     * USE        ：Output the log
     * ARGUMENT   ：none
     * RETURN     ：FALSE
     *              TRUE
     **********************************************************************/
    public function resultlog()
    {
        global $conf;
        global $msg;

        $facility = $conf["LogFacility"];

        /* Value of the facility */
        $facilitynames = array(
                               "auth"     => LOG_AUTH,
                               "authpriv" => LOG_AUTHPRIV,
                               "cron"     => LOG_CRON,
                               "daemon"   => LOG_DAEMON,
                               "kern"     => LOG_KERN,
                               "lpr"      => LOG_LPR,
                               "mail"     => LOG_MAIL,
                               "news"     => LOG_NEWS,
                               "syslog"   => LOG_SYSLOG,
                               "user"     => LOG_USER,
                               "uucp"     => LOG_UUCP,
                               "local0"   => LOG_LOCAL0,
                               "local1"   => LOG_LOCAL1,
                               "local2"   => LOG_LOCAL2,
                               "local3"   => LOG_LOCAL3,
                               "local4"   => LOG_LOCAL4,
                               "local5"   => LOG_LOCAL5,
                               "local6"   => LOG_LOCAL6,
                               "local7"   => LOG_LOCAL7
                           );

        /* Determination of the facility */
        if (isset($facility) === TRUE) {
            $facility = $facilitynames[$facility];
        } 

        /* Open the log */
        $ret = openlog(IDENT, LOG_PID, $facility);
        if ($ret === FALSE) {
            return FALSE;
        }

        /* Return FALSE if there is no message */
        if (!isset($msg[LANG][$this->code])) {
            return FALSE;
        }

        /* Return FALSE if there is no log */
        if ($this->message === "NO_LOG") {
            return TRUE;
        }

        /* Shaping the message */
        $output = vsprintf($this->file. ":".$this->line. ":". 
                          $msg[LANG][$this->code]["log"], $this->args);

        /* Output the log */
        $ret = syslog(LOG_ERR, $output);
        closelog();

        return TRUE;
    }
}

/***************************************************************************
 * CLASS NAME   ：SystemCrit
 * USE          ：Processing of exception screen display is not possible
 *                Split applications by extends the ExceptionWarn
 **************************************************************************/
class SystemCrit extends SystemWarn {
    /* Processing is empty, but I am using this class! */
}

?>
