<?php
/***************************************************************************
 * Class name   : libmsqualldeliver.php
 * description  : Class for send to mail
 * property     : $errcode
 **************************************************************************/
Class msquallstatus extends Validation {

    public $start_time;
    public $dir = "";
    public $errcode = 0;
    public $sender = "";
    private $num = 1;
    public $jp_char = "ISO-2022-JP";
    public $utf8_char = "UTF-8";

    /************************************************************************
     * method name : sendConfirmation()
     * description : To the confirmation before sending the e-mail.
     * args        : $start_time
     * return      : True or False
     ***********************************************************************/
    public function sendConfirmation($sendflg)
    {

        global $conf;

        $logObj = new SystemWarn();

        /* Directory existence check */
        $ret = $this->dirExist(STATUS);
        if ($ret === FALSE) {
            throw new SystemWarn($this->errcode, STATUS);
        }

        /* Directory readable check */
        $ret = $this->fileReadable(STATUS);
        if ($ret === FALSE) {
            throw new SystemWarn(NO_READ_DIR, STATUS);
        }

        /* Open the derectory */
        $dh = opendir(STATUS);
        if ($dh === FALSE) {
            throw new SystemWarn(NOT_OPEN_DIR, STATUS);
        }

        /* Read the directory */
        while (FALSE !== ($dir = readdir($dh))) {

            /* value initialization */
            $is_envelope = "";
            $is_envelope_bz2 = "";
            $is_send_log = "";
            $is_send_log_bz2 = "";
            $envelope_data = "";
            $fh = "";
            $bzfh = "";
            $data = array();
            $log_data = array();
            $ret = "";

            /* Skip to the dot file */
            $pos = strpos($dir, ".");
            if ($pos !== FALSE) {
                continue;
            }

            /* Create the directory path*/
            $dir = STATUS . $dir;

            /* Skip to the file */
            if ($this->dirExist($dir) === FALSE) {
               continue;
            }

            /* $sendflg 0 : reservation delivery
               $sendflg 1 : test delivery
               $sendflg 2 : immediate delivery
             */

            /* Create flag_file path */
            $testfile = $dir . SENDTEST;
            $immdfile = $dir . SENDIMMD;

            /* Check flag and flag_file */
            if ($sendflg === 0) {
                /* Reservation delivery and exist test flag_file */
                if ($this->fileExist($testfile) === TRUE) {
                    continue;
                }

                /* Reservation delivery and exist immd flag_file */
                if ($this->fileExist($immdfile) === TRUE) {
                    continue;
                }
            }

            /* Test delivery and not exist test flag_file */
            if ($sendflg === 1) {
                if ($this->fileExist($testfile) === FALSE) {
                    continue;
                }
            }

            /* Immediate delivery and not exist immd flag_file */
            if ($sendflg === 2) {
                if ($this->fileExist($immdfile) === FALSE) {
                    continue;
                }
            }

            /* Create the send.lock file path */
            $lock = $dir . SENDLOCK;

            /* Skip to when there is a send.lock file */
            if ($this->fileExist($lock) === TRUE) {
                continue;
            }

            /* Create the send_hole file path */
            $send_hold = $dir . SENDHOLD;

            /* Skip to when there is a send_hold file */
            if ($this->fileExist($send_hold) === TRUE) {
                continue;
            }

            /* Check the sending time */
            $ret = $this->checkTime($dir);
            if ($ret === -2) {
                continue;
            } else if ($ret === FALSE) {
                $logObj->code = INVALID_SENDTIME;
                $logObj->args = $this->args;
                $logObj->resultlog();
                continue;
            }

            /* Create the lock file */
            $ret = touch($lock);
            if ($ret === FALSE) {
                $logObj->code = CREATE_FILE_FAIL;
                $logObj->args = $lock;
                $logObj->resultlog();
            }

            /* Create the start-end_time file path */
            $start_end_file = $dir . STARTEND;
            $start_unix_time = $this->start_time;

            /* Skip to when there is a start-end_time file */
            $start_end_exist = $this->fileExist($start_end_file);
            if ($start_end_exist === TRUE) {
                $start_end = $this->readStartend($start_end_file);
                if ($start_end === FALSE) {
                    $logObj->code = $this->errcode;
                    $logObj->args = $start_end_file;
                    $logObj->resultlog();
                    continue;
                }
                $num = count($start_end);
                if ($num > 2) {
                    $logObj->code = INVALID_START_END;
                    $logObj->args = $start_end_file;
                    $logObj->resultlog();
                    continue;

                } else if ($num === 2) {
                    $ret = unlink($lock);
                    if ($ret === FALSE) {
                        $logObj->code = DELETE_FILE_FAIL;
                        $logObj->args = $lock;
                        $logObj->resultlog();
                        continue;
                    }
                    continue;
                }
            }

            /* Create the start-end_time file */
            if ($start_end_exist === FALSE || $num === 0) {
                $start_time = $this->makeStarttime($start_unix_time);
                if ($start_time === FALSE) {
                    $logObj->code = CREATE_TIME_FAIL;
                    $logObj->resultlog();
                    continue;
                }

                /* Write the start time */
                $ret = $this->writeFile($start_end_file, $start_time);
                if ($ret === FALSE) {
                    $logObj->code = CREATE_FILE_FAIL;
                    $logObj->args = $start_end_file;
                    $logObj->resultlog();
                    continue;
                }
            }

            /* Create the envelope file path */
            $line = array();
            $value = "";
            $envelope = $dir . ENVELOPE;
            $envelope_bz2 = $dir . ENVELOPE . ".bz2";

            /* Create the sending_err file path */
            $senderr = $dir . SENDERR;

            /* Throw to when there is a envelope file */
            $is_envelope = $this->fileExist($envelope);
            $is_envelope_bz2 = $this->fileExist($envelope_bz2);
            if ($is_envelope === FALSE && $is_envelope_bz2 === FALSE) {
                    $ret = touch($senderr);
                    $logObj->code = NO_SUCH_FILE;
                    $logObj->args = $envelope;
                    $logObj->resultlog();
                    continue;
            }

            /* Read the envelope file */
            if ($is_envelope_bz2 === TRUE) {

                /* Create the temporary file of envelope */
                $envelope_tmp = $envelope_bz2 . ".tmp";

                $bzfh = bzopen($envelope_bz2, "r");
                if ($bzfh === FALSE) {
                    $ret = touch($senderr);
                    $logObj->code = OPEN_FILE_FAIL;
                    $logObj->args = $envelope_bz2;
                    $logObj->resultlog();
                    continue;
                }

                while (!feof($bzfh)) {
                    $envelope_data .= bzread($bzfh);
                }

                $tmp_fh = fopen($envelope_tmp, "w");
                if ($tmp_fh === FALSE) {
                    $ret = touch($senderr);
                    $logObj->code = OPEN_FILE_FAIL;
                    $logObj->args = $envelope;
                    $logObj->resultlog();
                    continue;
                }

                $ret = fwrite($tmp_fh, $envelope_data);
                if ($ret === FALSE) {
                    continue;
                }
                fclose($tmp_fh);

                $fh_tmp = fopen($envelope_tmp, "r");
                if ($fh_tmp === FALSE) {
                    $ret = touch($senderr);
                    $logObj->code = OPEN_FILE_FAIL;
                    $logObj->args = $envelope;
                    $logObj->resultlog();
                    continue;
                }

                while (!feof($fh_tmp)) {
                    $data[] = fread($fh_tmp, filesize($envelope_tmp));
                }
                fclose($fh_tmp);

                $ret = unlink($envelope_tmp);
                if ($ret === FALSE) {
                    $logObj->code = DELETE_FILE_FAIL;
                    $logObj->args = $envelope_tmp;
                    $logObj->resultlog();
                    continue;
                }
            }

            if ($is_envelope === TRUE && $is_envelope_bz2 === FALSE) {
                $fh = fopen($envelope, "r");
                if ($fh === FALSE) {
                    $ret = touch($senderr);
                    $logObj->code = OPEN_FILE_FAIL;
                    $logObj->args = $envelope;
                    $logObj->resultlog();
                    continue;
                }

                while (!feof($fh)) {
                    $data[] = fread($fh, filesize($envelope));
                }

            }

            $line = explode("\n", $data[0]);
            $ret = $this->__checkEnvelope($line);
            if ($ret === FALSE) {
                $ret = touch($senderr);
                $logObj->code = INVALID_FILE;
                $logObj->args = $envelope;
                $logObj->resultlog();
                continue;
            }
           
            /* Create the send_log file path */
            $send_log = $dir . SENDLOG;
            $send_log_bz2 = $dir . SENDLOG . ".bz2";

            /* Check the send_log file */
            $log_addr = array();
            $log_line = array();
            $log_value = "";
            $log_tmp_data = "";
            $is_send_log = $this->fileExist($send_log);
            $is_send_log_bz2 = $this->fileExist($send_log_bz2);
            if ($is_send_log === TRUE || $is_send_log_bz2 === TRUE) {

                /* Read the send_log file */
                if ($is_send_log_bz2 === TRUE) {

                    $bzfh = bzopen($send_log_bz2, "r");
                    if ($bzfh === FALSE) {
                        $logObj->code = OPEN_FILE_FAIL;
                        $logObj->args = $send_log_bz2;
                        $logObj->resultlog();
                        continue;
                    }

                    while (!feof($bzfh)) {
                        $log_tmp_data .= bzread($bzfh);
                    }

                    $send_log_tmp = $envelope_bz2 . ".tmp";
                    $tmp_fh = fopen($send_log_tmp, "w");
                    if ($tmp_fh === FALSE) {
                        $ret = touch($senderr);
                        $logObj->code = OPEN_FILE_FAIL;
                        $logObj->args = $send_log;
                        $logObj->resultlog();
                        continue;
                    }

                    $ret = fwrite($tmp_fh, $log_tmp_data);
                    if ($ret === FALSE) {
                        continue;
                    }
                    fclose($tmp_fh);

                    $fh_tmp = fopen($send_log_tmp, "r");
                    if ($fh_tmp === FALSE) {
                        $ret = touch($senderr);
                        $logObj->code = OPEN_FILE_FAIL;
                        $logObj->args = $envelope;
                        $logObj->resultlog();
                        continue;
                    }

                    while (!feof($fh_tmp)) {
                        $data[] = fread($fh_tmp, filesize($send_log_tmp));
                    }
                    fclose($tmp_fh);
    
                    $ret = unlink($send_log_tmp);
                    if ($ret === FALSE) {
                        $logObj->code = DELETE_FILE_FAIL;
                        $logObj->args = $lock;
                        $logObj->resultlog();
                        continue;
                    }
                }

                if ($is_send_log === TRUE && $is_send_log_bz2 === FALSE) {
                    $fh = fopen($send_log, "r");
                    if ($fh === FALSE) {
                        $logObj->code = OPEN_FILE_FAIL;
                        $logObj->args = $send_log;
                        $logObj->resultlog();
                        continue;
                    }

                    while (!feof($fh)) {
                        $log_data[] = fread($fh, filesize($send_log));
                    }

                }

                $log_line = explode("\n", $log_data[0]);
                /* Separated by tab log */
                foreach($log_line as $log_value) {
                    if ($log_value === "") {
                        continue;
                    }
                    $log_column = explode("\t", $log_value);
                    $log_addr[] = $log_column[2];
               }
            }

            /* Send e-mail recipient minute */
            foreach($line as $value) {

                /* Skip to when there is a send_hold file */
                if ($this->fileExist($send_hold) === TRUE) {
                    $ret = unlink($lock);
                    if ($ret === FALSE) {
                        $logObj->code = DELETE_FILE_FAIL;
                        $logObj->args = $lock;
                        $logObj->resultlog();
                    }
                    continue 2;
                }

                if ($value === "") {
                    continue;
                }

                $to_addr = "";
                $from_addr = "";
                $output = "";

                /* Read the mailinfo file */
                $mailcontent = $this->__chMailtext($dir, $value, $sendflg, $log_addr, $to_addr, $from_addr);
                if ($mailcontent === FALSE) {
                    $ret = touch($senderr);
                    $logObj->code = INVALID_MAILINFO;
                    $logObj->args = $dir . MAILINFO;
                    $logObj->resultlog();
                    continue 2;
                } else if ($mailcontent === -2) {
                    continue;
                }

                /* Send mail */
                $ret = popen("" . $conf["SendMail"] . " " . "-f $from_addr $to_addr", "w");
                fputs($ret, $mailcontent);
                pclose($ret);

                $send_time = date("Ymd-His");
                if ($ret !== FALSE) {
                    $output = "OK" . "\t" . $send_time . "\t" . $to_addr . "\n";
                } else {
                    $output = "NG " . "\t" . $send_time . "\t" . $to_addr . "\n";
                }

                /* Make the temporary file name*/
                $log_file = $dir . SENDLOG;
                $bzlog_file = $dir . SENDLOG . ".bz2";

                /* Create the sending_log file */
                if ($this->fileExist($bzlog_file) === TRUE) {
                    $ret = $this->writeBzfile($bzlog_file, $output);
                    if ($ret === FALSE) {
                        $logObj->code = CREATE_FILE_FAIL;
                        $logObj->args = $log_file;
                        $logObj->resultlog();
                    }
                } else {
                    $ret = $this->writeFile($log_file, $output);
                    if ($ret === FALSE) {
                        $logObj->code = CREATE_FILE_FAIL;
                        $logObj->args = $log_file;
                        $logObj->resultlog();
                    }
                }

            }
            /* Convert sending_log file into bz2 file*/
            $send_log = $dir . SENDLOG;
            if ($this->fileExist($send_log) === TRUE) {
                $ret = $this->changeBz2_file($send_log);
                if ($ret === FALSE) {
                    $logObj->code = $this->errcode;
                    $logObj->args = $send_log;
                    $logObj->resultlog();
                }
            }

            /* Write the end time */
            $start_time = $this->makeStarttime(time());
            if ($start_time === FALSE) {
                $logObj->code = CREATE_FILE_FAIL;
                $logObj->args = $start_end_file;
                $logObj->resultlog();
                continue;
            }

            $ret = $this->writeFile($start_end_file, $start_time);
            if ($ret === FALSE) {
                $logObj->code = CREATE_FILE_FAIL;
                $logObj->args = $start_end_file;
                $logObj->resultlog();
                continue;
            }

            $ret = unlink($lock);
            if ($ret === FALSE) {
                $logObj->code = DELETE_FILE_FAIL;
                $logObj->args = $lock;
                $logObj->resultlog();
                continue;
            }
        } 
        return TRUE;
    }

    /************************************************************************
     * method name : readStartend()
     * description : Check the start time.
     * args        : 
     * return      : True or False
     ***********************************************************************/
    public function readStartend($start_end_file)
    {

        if ($this->fileReadable($start_end_file) === FALSE) {
            $this->errcode = OPEN_FILE_FAIL;
            return FALSE;
        }

        /* Read one file */
        $start_end = file($start_end_file);
        if ($start_end === FALSE) {
            $this->errcode = READ_FILE_FAIL;
            return FALSE;
        }

        return $start_end;
    }

    /************************************************************************
     * method name : makeStarttime()
     * description : Check the start time.
     * args        : 
     * return      : True or False
     ***********************************************************************/
    public function makeStarttime($change_time)
    {

        $write_time = date("Y/m/d H:i:s", $change_time);
        if ($write_time === FALSE) {
            return FALSE;
        }

        $write_time = $write_time . "\n";
        return $write_time;

    }

    /************************************************************************
     * method name : checkTime()
     * description : Check the start time.
     * args        : 
     * return      : True or False
     ***********************************************************************/
    public function checkTime($dir, &$send_time = "")
    {

        $start_time = $this->start_time;
        $timefile = $dir . SENDTIME;
        $number = "0123456789";
        $lowercase = "abcdefghijklmnopqrstuvwxyz";
        $sym = "-";
        $admin = $number . $lowercase . $sym;
        $line = array();

        /* Check exist the time file */
        if ($this->fileExist($timefile) === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        /* Check read the time file */
        if ($this->fileReadable($timefile) === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        /* Open the time file */
        $fh = fopen($timefile, "r");
        if ($fh === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        $num = 0;

        /* Check the time */
        while (($line["$num"] = fgets($fh)) !== FALSE) {

            $line["$num"] = rtrim($line["$num"]);
            $num++;
        }

        if ($line["0"] === FALSE || $line["1"] === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        if ($line["2"] !== FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        $len = strlen($line["1"]);
        if ($len < 4 || $len > 16) {
            $this->args = $timefile;
            return FALSE;
        }

        if ($this->strtype($line["1"], $admin) === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        if ($this->strtype($line["0"], $number) === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        if (strlen($line["0"]) !== 14) {
            $this->args = $timefile;
            return FALSE;
        }

        $year = substr($line["0"], 0, 4);
        $mon = substr($line["0"], 4, 2);
        $day = substr($line["0"], 6, 2);

        /*  Check format YYYY, MM, DD */
        $ymd = checkdate($mon, $day, $year);
        if ($ymd === FALSE) {
            $this->args = $timefile;
            return FALSE;
        }

        $hour = substr($line["0"], 8, 2);
        $minutes = substr($line["0"], 10, 2);
        $seconds = substr($line["0"], 12, 2);

        /* Check to hour, minutes, seconds. */
        if ($hour < 0 || $hour > 23) {
            $this->args = $timefile;
            return FALSE;
        }

        if ($minutes < 0 || $minutes > 59) {
            $this->args = $timefile;
            return FALSE;
        }

        if ($seconds < 0 || $seconds > 59) {
            $this->args = $timefile;
            return FALSE;
        }

        $send_time = strtotime($year . "-" . $mon . "-" . $day . " " . $hour . ":" . $minutes . ":" . $seconds);

        if ( $send_time > $start_time) {
            return -2;
        }

        return TRUE; 

    }

    /************************************************************************
     * method name : checkExtension()
     * description : Check the extension.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
     private function __checkExtension($file) {
        
        $extension = substr('$file', -4);

        /* Check the extension */
        if ($extension !== ".bz2") { 
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : checkEnvelope()
     * description : Check the extension.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    private function __checkEnvelope($data) {

        /* Initialize the variables */
        $num = 0;
        $disp_mess = "";
        $mess = array();

        /* Read one line */
        foreach($data as $line) {
            /* Increase the number of lines */
            $num++;

            /* Skip blank line */
            if ($line === "") {
                continue;
            }

            /* Separated by tab */
            $column = explode("\t", $line);
            if ($column === FALSE) {
                $mess[] = $num;
                continue;
            }

            $num_column = count($column);
            if ($num_column <= 0 || $num_column > 6) {
                $mess[] = $num;
                continue;
            }

            /* Check the bytes of columns */
            foreach ($column as $value) {
                if (strlen($value) > 512) {
                    $mess[] = $num;
                    continue 2;
                }
            }

            /* Check the e-mail address */
            $column[0] = trim($column[0]);
            $ret = $this->email($column[0]);
            if ($ret === FALSE) {
                $mess[] = $num;
                continue;
            }
        }
        
        if ($mess !== array()) {
            return FALSE;
        }

        return TRUE; 
    }

    /************************************************************************
     * method name : chMailtext()
     * description : Check the extension.
     * args        : $data
     * return      : True or False
     ***********************************************************************/
    private function __chMailtext($dir, $value, $sendflg, $log_addr = array(), &$to_addr = "", &$from_addr = "") {


        global $conf;
        $num = -1;
        $tag = array("{WORD1}" => "",
                     "{WORD2}" => "",
                     "{WORD3}" => "",
                     "{WORD4}" => "",
                     "{WORD5}" => "");

        /* Separated by tab envelope */
        $column = explode("\t", $value);
        $to_addr = $column[0];

        /* Check whether sent mail */
        if ($log_addr !== array()) {
            foreach($log_addr as $send_addr) {
                if ($send_addr === $to_addr) {
                    return -2;
                }
            }
        }

        /* Assignment in WORD */
        foreach($column as $replace) {
            $num++;
            if($num === 0) {
                continue;
            }
            $tag["{WORD" . $num  . "}"] = $replace;
        }

        /* Create the mailinfo file path */
        $mailinfo = $dir . MAILINFO;

        /* Check the mailinfo file */
        if ($this->fileExist($mailinfo) === FALSE) {
            return FALSE;
        }

        $fh = fopen($mailinfo, "r");
        if ($fh === FALSE) {
            return FALSE;
        }

        $num = 0;
        $head = 0;
        $text = "";
        /* Divided into a header and body files mailinfo */
        while (!feof($fh)) {
            if($head === 0) {
                $header[] = fgets($fh);
                $header["$num"] = rtrim($header["$num"]);
                $len = strlen($header["$num"]);
                if (strlen($header["$num"]) === 0) {
                    $head = 1;
                }
            } else {
               $text .= fgets($fh);
            }
            $num++;
        }

        /* UTF-8 encoded */
        $en_text = mb_convert_encoding($text, $this->utf8_char, $this->jp_char);
        
        /* Change the mail tag */
        if ($sendflg !== 1) {
            foreach($tag as $word => $replace) {
                $en_text = str_replace($word, $replace, $en_text);
            }
        }
         
        /* ISO-2022-JP encoded */
        $mail_text = mb_convert_encoding($en_text, $this->jp_char, $this->utf8_char);

        /* Create the mail */
        $ch_text = "";
        foreach ($header as $head) {
            $ch_text .= $head . "\r\n";
        }
        $ch_text .= "\r\n";
        $ch_text .= $mail_text . "\r\n";

        /* Create the from address */
        $from = hash("sha256", $to_addr);
        $from_addr = $from . "@" . $conf["FromDomain"];

        return $ch_text;

    }


    /**********************************************************************
     * METHOD NAME：writeFile()
     * USE        ：Create the file .
     * ARGUMENT   ：
     * RETURN     ：
     **********************************************************************/
    public function writeFile($file, $data)
    {

        /* Open the temporary file */
        $fh = fopen($file, "a");
        if ($fh === FALSE) {
            return TRUE;
        }

        /* Write the data */
        $ret = fwrite($fh, $data);
        if ($ret === FALSE) {
            fclose($fh);
            return TRUE;
        }

        /* unlock and close the send_log file */
        fclose($fh);

        return TRUE;

    }

    /**********************************************************************
     * METHOD NAME：writeFile()
     * USE        ：Create the Bzfile .
     * ARGUMENT   ：
     * RETURN     ：
     **********************************************************************/
    public function writeBzfile($file, $data)
    {

       /* open a file in a format bz2 primary file */
        $bz_fh = bzopen($file, "r");
        if ($bz_fh === FALSE) {
            return FALSE;
        }
  
        while (!feof($bz_fh)) {
            $log_data .= bzread($bz_fh);
        }

        bzclose($bz_fh);
        $log_data .= $data;

        $bz_fh = bzopen($file, "w");
        if ($bz_fh === FALSE) {
            return FALSE;
        }

        /* Write bz2 file */
        $ret = bzwrite($bz_fh, $value);
        if ($ret === FALSE) {
            return FALSE;
        }

        return TRUE;
    }

    /************************************************************************
     * method name : changeBz2_file()
     * description : Create a bz2 file from the sending_log file.
     * args        : $file
     * return      : True
     ***********************************************************************/
    public function changeBz2_file($file)
    {

        /* Create a name for the extension bz2 format */
        $bz2_file = $file . ".bz2";

        /* Open the file to be converted into a bz2 file */
        $fh = fopen($file, "r");
        if ($fh === FALSE) {
            $this->errcode = OPEN_FILE_FAIL;
        }

        /* open a file in a format bz2 primary file */
        $bz_fh = bzopen($bz2_file, "w");
        if ($bz_fh === FALSE) {
            $this->errcode = NO_WRITE_FILE;
        }

        /* Read one line */
        while (($line = fgets($fh)) !== FALSE) {

            /* Write bz2 file */
            $ret = bzwrite($bz_fh, $line);
            if ($ret === FALSE) {
                $this->errcode = WRITE_FILE_FAIL;
            }

        }

        /* Close the file */
        fclose($fh);
        bzclose($bz_fh);

        /* Delete the file */
        $ret = unlink($file);
        if ($ret === FALSE) {
            $this->errcode = DELETE_FILE_FAIL;
        }

        return TRUE;

    }

}

?>
