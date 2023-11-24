<?php
/***************************************************************************
 * Class name   : Validation
 * description  : Class for data validation
 * property     : $errcode
 **************************************************************************/
include_once("../lib/dglibescape.php");

Class Validation {
    public $errcode = 0;
    public $errargs = array("");

    function fileExist($file)
    {
        /* Clear status cache*/
        clearstatcache();

        /* Existence confirmation of file */
        if (file_exists($file) === FALSE) {
            $this->errcode = NO_SUCH_FILE;
            return FALSE;
        }

        /* Specified path is a directory */
        if (is_dir($file) === TRUE) {
            $this->errcode = NO_SUCH_FILE;
            return FALSE;
        }

        return TRUE;
    }


    /************************************************************************
     * method name : fileReadable()
     * description : Checks whether the specified file is readable.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    function fileReadable($file)
    {
        /* Clear status cache*/
        clearstatcache();

        /* Have the right to read */
        if (is_readable($file) === FALSE) {
            $this->errcode = NO_READ_FILE;
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : fileWritable()
     * description : Checks whether the specified file is writable.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    function fileWritable($file)
    {
        /* Clear status cache*/
        clearstatcache();

        /* Have the right to write */
        if (is_writable($file) === FALSE) {
            $this->errcode = NO_WRITE_FILE;
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : fileExecutable()
     * description : Checks whether the specified file is executable.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    function fileExecutable($file)
    {
        /* Clear status cache*/
        clearstatcache();
        
        /* Have the right to exec */
        if (is_executable($file) === FALSE) {
            $this->errcode = NO_EXEC_FILE;
            return FALSE;
        }

        return TRUE;
    }
    
    /************************************************************************
     * method name : required
     * description : Checks the specified variable
     * args        : $var
     * return      : True or False
     ***********************************************************************/
    function required($var)
    {
        if (is_null($var) === TRUE || $var === "") {
            return FALSE;
        }
        return TRUE;
    }

    /************************************************************************
     * method name : dirExist
     * description : Checks the specified directory
     * args        : $dir
     * return      : True or False
     ***********************************************************************/
    function dirExist($dir)
    {
        if(is_dir($dir) === FALSE) {
            $this->errcode = NO_SUCH_DIR;
            return FALSE;
        }
        return TRUE;
    }

    /************************************************************************
     * method name : dateFormat_non_empty
     * description : Checks the specified date format non_empty
     * args        : $date
     * return      : True or False
     ***********************************************************************/
    function dateFormat_non_empty($date)
    {
    
        $num = "0123456789";

        if ($date === "") {
            return TRUE;
        }

        $data = explode(" ", $date, 2);
        if ($data === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        /* To check that there are two data */
        if (count($data) !== 2) {
            $this->errcode = "";
            return FALSE;
        }
    
        /* Split with [/] [YYYY/MM/DD] */
        $date = explode("/", $data[0], 3);
        if ($date === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        /* To check that there are three data */
        if (count($date) !== 3) {
            $this->errcode = "";
            return FALSE;
        }
    
        if (strspn($date[0], $num) != strlen($date[0])) {
            $this->errcode = "";
            return FALSE;
        }

        if (strspn($date[1], $num) != strlen($date[1])) {
            $this->errcode = "";
            return FALSE;
        }

        if (strspn($date[2], $num) != strlen($date[2])) {
            $this->errcode = "";
            return FALSE;
        }

        /*  Check format YYYY, MM, DD */
        $year = checkdate($date[1], $date[2], $date[0]);
        if ($year === FALSE) {
            $this->errcode = "";
            return FALSE;
        }
    
        $time = explode(":", $data[1], 2);
        if ($time === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        /* To check that there are two data */
        if (count($time) !== 2) {
            $this->errcode = "";
            return FALSE;
        }
    
        if (strspn($time[0], $num) != strlen($time[0])) {
            $this->errcode = "";
            return FALSE;
        }

        if (strspn($time[1], $num) != strlen($time[1])) {
            $this->errcode = "";
            return FALSE;
        }

        if ($time[0] < 0 || $time[0] > 23) {
            $this->errcode = "";
            return FALSE;
        }

        if ($time[1] < 0 || $time[1] > 59) {
            $this->errcode = "";
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : dateFormat
     * description : Checks the specified date format
     * args        : $date
     * return      : True or False
     ***********************************************************************/
    function dateFormat($date)
    {
        /*NULL character check */
        if (is_null($date) === TRUE) {
            $this->errcode = "";
            return FALSE;
        }
    
        if ($date === "") {
            $this->errcode = "";
            return FALSE;
        }

        $date = $this->dateFormat_non_empty($date);
        if ($date === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : dateValue
     * description : Checks the specified date value.
     * args        : $date
     * return      : True or False
     ***********************************************************************/
    function dateValue($date)
    {
        /*NULL character check */
        if (is_null($date) === TRUE) {
            $this->errcode = "";
            return FALSE;
        }
    
        $data = explode(" ", $date, 2);
        if ($data === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        /* To check that there are two data */
        if (count($data) !== 2) {
            $this->errcode = "";
            return FALSE;
        }
    
        /* Split with [/] [YYYY/MM/DD] */
        $date = explode("/", $data[0], 3);
        if ($date === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        /* To check that there are three data */
        if (count($date) !== 3) {
            $this->errcode = "";
            return FALSE;
        }
    
        $time = explode(":", $data[1], 2);
        if ($time === FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        /* To check that there are two data */
        if (count($time) !== 2) {
            $this->errcode = "";
            return FALSE;
        }

        $rsrv_time = date("U", mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]));
        $now = time();

        /* To check that reservation time is past or future. */
        if ($rsrv_time < $now) {
            $this->errcode = "";
            return FALSE;
        }   

        return TRUE;
    }

    /************************************************************************
     * method name : email
     * description : Checks the specified mail address format
     * args        : $mail
     * return      : True or False
     ***********************************************************************/
    function email($mail)
    {
        $buf = explode('@', $mail, 2);
        if (count($buf) !== 2) {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        if ($buf[0] == "" || $buf[1] == "") {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        $num = "0123456789";
        $sl = "abcdefghijklmnopqrstuvwxyz";
        $ll = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $sym1 = "!#$%&'*+-/=?^_{}~.";
        $sym2 = "-.";

        $localpart = $num . $sl . $ll . $sym1;
        $domainpart = $num . $sl . $ll . $sym2;

        if (strspn($buf[0], $localpart) != strlen($buf[0])) {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        if (strlen($buf[1]) < 3) {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        if (substr($buf[1], 0, 1) == ".") {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        if (strpos($buf[1], ".") === FALSE) {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        if (strpos($buf[1], "..") !== FALSE) {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        if (strspn($buf[1], $domainpart) != strlen($buf[1])) {
            $this->errcode = INVALID_MADDR;
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : facilityFormat
     * description : Checks the specified syslog facility
     * args        : $facility
     * return      : True or False
     ***********************************************************************/
    function facility($facility)
    {
        $flist = array("auth", "authpriv", "cron", "daemon", "kern", "local0",
                       "local1", "local2", "local3", "local4", "local5",
                       "local6", "local7", "lpr", "mail", "news", "syslog",
                       "user", "uucp");

        $ret = array_search($facility, $flist);
        if ($ret === FALSE) {
            $this->errcode = INVALID_LOGFACILITY;
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : strtype
     * description : Checks the type of string
     * args        : $str
     *               $type
     * return      : True or False
     ***********************************************************************/
    public function strtype($str, $type)
    {
        if (strspn($str, $type) != strlen($str)) {
            return FALSE;
        }
        return TRUE;
    }

    /************************************************************************
     * method name : gt
     * description : Checks the length of string
     * args        : $str
     *               $border
     * return      : True or False
     ***********************************************************************/
    public function gt($str, $border)
    {
        $len = $this->__strlen($str);
        if ($len > $border) {
            return TRUE;
        }
        return FALSE;
    }

    /************************************************************************
     * method name : lt
     * description : Checks the length of string
     * args        : $str
     *               $border
     * return      : True or False
     ***********************************************************************/
    public function lt($str, $border)
    {
        $len = $this->__strlen($str);
        if ($len < $border) {
            return TRUE;
        }
        return FALSE;
    }

    /************************************************************************
     * method name : ge
     * description : Checks the length of string
     * args        : $str
     *               $border
     * return      : True or False
     ***********************************************************************/
    public function ge($str, $border)
    {
        $len = $this->__strlen($str);
        if ($len >= $border) {
            return TRUE;
        }
        return FALSE;
    }

    /************************************************************************
     * method name : le
     * description : Checks the length of string
     * args        : $str
     *               $border
     * return      : True or False
     ***********************************************************************/
    public function le($str, $border)
    {
        $len = $this->__strlen($str);
        if ($len <= $border) {
            return TRUE;
        }
        return FALSE;
    }

    /************************************************************************
     * method name : __strlen
     * description : Checks the length of string
     * args        : $str
     * return      : length
     ***********************************************************************/
    private function __strlen($str)
    {
        $len = mb_strlen($str, "utf-8");
        if ($len === FALSE) {
            $len = strlen($str);
        }
        return $len;
    }

    /************************************************************************
     * method name : OneByteKana
     * description : Checks the subject
     * args        : $str
     * return      : TRUE or FALSE
     ***********************************************************************/
    public function OneByteKana($str)
    {
        $one_byte_kana = "[ｱ-ﾝ]";

        if (mb_ereg($one_byte_kana, $str) !== FALSE) {
            $this->errcode = "";
            return FALSE;
        }

        return TRUE;
    }
}

Class formValidation extends Validation {
    public function exec($rule)
    {
        foreach ($rule as $one) {
            $id = $one["id"];
            $label  = $one["label"];

            if (isset($_POST["$id"]) === FALSE) {
                throw new SystemWarn(INVALID_CONFIGURATION, $label);
            }

            foreach ($one["rule"] as $rule => $errcode) {
                $args = array($_POST["$id"]);
                $start = strpos($rule, '[') + 1;
                $end = strrpos($rule, ']');

                if ($rule === "noreq") {
                    if ($_POST["$id"] === "") {
                        break;
                    } else {
                        continue;
                    }
                }

                if ($start !== FALSE && $end !== FALSE) {
                    $method = substr($rule, 0, $start - 1);
                    $args[] = substr($rule, $start, strlen($rule) - $start - 1);
                } else {
                    $method = $rule;
                }
 
                $ch_id = escape_html($_POST["$id"]);
                $ret = call_user_func_array(array($this, $method), $args);
                if ($ret === FALSE) {
                    throw new SystemWarn($errcode, array($label, $ch_id));
                }
            }
        }
        return TRUE;
    }
}

?>
