<?php
/***************************************************************************
 * SESSION LIBRARY 
 **************************************************************************/

/***************************************************************************
 * CLASS NAME   ：sessionExecutive
 * USE          ：Session Management
 * PROPERTY     ：$sessname
 *                $effective_time
 *                $path
 *                $domain
 **************************************************************************/
Class sessionExecutive extends Validation {

    private $sessname = "maglo";
    private $effective_time = "";
    private $current_time = "";
    private $path = "/";
    private $domain = "";
    public $login = 0;

    /**********************************************************************
     * METHOD NAME：__construct()
     * USE        ：Initialization of session information
     * ARGUMENT   ：none
     * RETURN     ：none
     **********************************************************************/
    public function __construct()
    {

        global $conf;

        /* Definitin of current_time, effective_time */
        $this->current_time = time();
        $this->effective_time = $conf["SessionTimeout"];

        /* Definition of Cookie information(effective_time, path, domain) */
        $this->domain = $_SERVER["SERVER_NAME"];
    }

    /**********************************************************************
     * METHOD NAME：startsess()
     * USE        ：Start the session
     * ARGUMENT   ：$id
     *              $pass
     *              $flg login_screen:1 other_screen(default):0
     * RETURN     ：TRUE
     *              FALSE
     **********************************************************************/
    public function startSess($id = 0, $pass = 0)
    {

        /* Define the session name */
        session_name($this->sessname);

        /* Setting the session cookie parameters */
        session_set_cookie_params($this->effective_time, $this->path, $this->domain);

        /* Cookie start of the session is also created automatically */
        $res = session_start();
        if ($res === FALSE) {
            throw new SystemWarn(START_SESSION_ERROR);
        }

        /* Stored in a session variable (Login)*/
        if ($this->login === 1) {
            $_SESSION["start_time"] = $this->current_time;
            $_SESSION["user"] = $id;
            $_SESSION["pass"] = $pass;
        }

        return TRUE;
    }

    /**********************************************************************
     * METHOD NAME：check_sess()
     * USE        ：Examination of the effectiveness of the session
     * ARGUMENT   ：$flg login_screen:1 other_screen(default):0
     * RETURN     ：TRUE
     *              FALSE
     **********************************************************************/
    public function checkSess()
    {
        
        /* Stored in a session variable (Login)*/
        if ($this->login === 1) {
            $res = $this->__checkPass();
            if ($res === FALSE) {
                throw new SystemWarn(INVALID_ID);
            }
        }

        try {
            /* Error if there is no cookie */
            if (empty($_SESSION)) {
                throw new SystemWarn(SESSION_EMPTY);
            }

            /* Return FALSE start_time if there is no */
            if (!isset($_SESSION["start_time"]) || $_SESSION["start_time"] === "") {
                throw new SystemWarn(SESSION_ERROR);
            }

            /* Return FALSE user if there is no */
            if (!isset($_SESSION["user"]) || $_SESSION["user"] === "") {
                throw new SystemWarn(SESSION_ERROR);
            }

            /* Return FALSE pass if there is no */
            if (!isset($_SESSION["pass"]) || $_SESSION["pass"] === "") {
                throw new SystemWarn(SESSION_ERROR);
            }

            /* Check the expiration time of the session */
            $res = $this->__checkTime();
            if ($res === FALSE) {
                throw new SystemWarn(TIME_OUT);
            }

            /* Check the id and pass of the session */
            $res = $this->__checkpass();
            if ($res === FALSE) {
                throw new SystemWarn(SESSION_ERROR);
            }

        } catch (SystemWarn $err) {
            if ($this->login === 2) {
                throw new SystemWarn($err->code);
            } else {
                setcookie("direct", 1);
                header("Location: ../htdocs/index.php");
                exit(0);
            }
        }
        return TRUE;
    }

    /**********************************************************************
     * METHOD NAME：destroy_sess()
     * USE        ：Destroying a session
     * ARGUMENT   ：none
     * RETURN     ：TRUE
     *              FALSE
     **********************************************************************/
    public function destroySess()
    {
        /* Delete of Cookie */
        setcookie("$this->sessname", '', time() - 86400, $this->path, $this->domain);

        /* Destroying a session */
        $res = session_destroy();
        if ($res === FALSE) {
            return FALSE;
        }
    
        return TRUE;
    }

    /**********************************************************************
     * METHOD NAME：readPassfile()
     * USE        ：Reads the password file, and returns a value
     * ARGUMENT   ：none
     * RETURN     ：
     *              FALSE
     **********************************************************************/
    public function readPassfile()
    {

        $filename = PASSFILE;

        /* Presence check */
        $ret = $this->fileExist($filename);
        if ($ret === FALSE) {
            throw new SystemWarn($this->errcode, $filename);
        }
        /* Read access check */
        $ret = $this->fileReadable($filename);
        if ($ret === FALSE) {
            throw new SystemWarn(READ_ADMIN_ERROR, $filename);
        }

        /* open the password file */
        $fp = fopen($filename, "r");
        if ($fp === FALSE) {
            throw new SystemWarn(READ_ADMIN_ERROR, $filename);
        }

        /* Initialization */
        $line = 0;

        /* Read the file */
        while (feof($fp) === FALSE) {

            /* The buffer the one line */
            $buf = fgets($fp);
            if ($buf === FALSE) {
                break;
            }

            /* Remove line breaks and white space at the end of the line */
            $buf = rtrim($buf);

            $line++;

            /* Ignored head of the line if the comment line '#' */
            $c = substr($buf, 0, 1);
            if ($c == "#" || $c == "") {
                continue;
            }

            /* Divided by the delimiter at the beginning of the line */
            $data = explode(":", $buf);

            if (isset($data[1]) === FALSE) {
                throw new SystemWarn(ADMIN_INVALID, $line);
            }

            /* Beginning to null, and If a parameter is blank value, error */
            if (($data[0] == "") || ($data[1] == "")) {
                throw new SystemWarn(ADMIN_INVALID, $line);
            }

            /* Store of value */
            $pass[$line][$data[0]] = $data[1];
        }

        return $pass;
        
    }

    /**********************************************************************
     * METHOD NAME：__checkTime()
     * USE        ：Check the expiration time of the session
     * ARGUMENT   ：none
     * RETURN     ：TRUE
     *              FALSE
     **********************************************************************/
    private function __checkTime()
    {
        /* Get UNIXTIME start of the session from the session variable */
        $start_time = $_SESSION["start_time"];

        /* Calculate the difference between currenttime and starttime */
        $diff_time = time() - $start_time;

        /* Past the time period, return FALSE */
        if ($this->effective_time < $diff_time) {
             return FALSE;
        }

        return TRUE;
    }

    /**********************************************************************
     * METHOD NAME：__checkpass()
     * USE        ：Check the ID and password of the session
     * ARGUMENT   ：none
     * RETURN     ：TRUE
     *              FALSE
     **********************************************************************/
    private function __checkPass()
    {
        if (!isset($_SESSION["user"]) || $_SESSION["user"] === "") {
            return FALSE;
        }
        if (!isset($_SESSION["pass"]) || $_SESSION["pass"] === "") {
            return FALSE;
        }
        /* Get user, pass, sess_id from cookies */
        $id = $_SESSION["user"];
        $pass = $_SESSION["pass"];

        /* Get ID, and PASS from the session file */
        $ret = call_user_func("sessionExecutive". '::readPassfile');
        if ($ret === FALSE) {
            return FALSE;
        }

        /* Compare and pass ID */
        foreach ($ret as $num) {
            foreach ($num as $key => $value) {
                if ($key === $id && $value === $pass) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
}

?>
