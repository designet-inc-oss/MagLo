<?php
/***************************************************************************
 * Class name   : status
 * description  : Class for status
 * property     : $errcode
 **************************************************************************/
include_once("../lib/dglibescape.php");

Class status extends Validation {

    /************************************************************************
     * method name : checkDifftime()
     * description : Check the search time.
     * args        : $start_time, $end_time
     * return      : True or False
     ***********************************************************************/

    public function checkDifftime(&$search_s_time, &$search_e_time)
    {

        if ($search_s_time !== "") {
            $s_date = explode(" ", $search_s_time, 2);
            $s_YMD = explode("/", $s_date[0], 3);
            $s_hm = explode(":", $s_date[1], 2);
            $search_s_time = strtotime($s_YMD[0] . "-" . $s_YMD[1] . "-" . $s_YMD[2] . " " . $s_hm[0] . ":" . $s_hm[1]);
        }

        if ($search_e_time !== "") {
            $e_date = explode(" ", $search_e_time, 2);
            $e_YMD = explode("/", $e_date[0], 3);
            $e_hm = explode(":", $e_date[1], 2);
            $search_e_time = strtotime($e_YMD[0] . "-" . $e_YMD[1] . "-" . $e_YMD[2] . " " . $e_hm[0] . ":" . $e_hm[1]);
        }

        if ($search_s_time !== "" && $search_e_time !== "") {
            if ($search_s_time > $search_e_time) {
                throw new SystemWarn(BEFORE_DATE);
            }
        }

        return TRUE;
    }

    /************************************************************************
     * method name : readindStatus()
     * description : Look for the data corresponding to the search date.
     * args        : $start_time, $end_time
     * return      : True or False
     ***********************************************************************/
    public function readingStatus($search_s_time, $search_e_time)
    {

        $data = array();

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

            $send_time = "";
            $starttime = "";
            $lasttime = "";
            $status = "";
            $num = 0;
            $en_num = 0;

            /* Skip to the dot file */
            $pos = strpos($dir, ".");
            if ($pos !== FALSE) {
                continue;
            }

            /* Create the directory path*/
            $disp_dir = $dir;
            $dir = STATUS . $dir;

            /* Skip to the file */
            if ($this->dirExist($dir) === FALSE) {
                continue;
            }

            /* Confirmation of the existence sendtime file */
            $timeObj = new msquallstatus();
            $ret = $timeObj->checkTime($dir, $send_time);
            if ($ret === FALSE) {
                continue;
            }

            /* The conversion to the format of the time display */
            $disp_date = date("Y/m/d H:i", $send_time);

            /* Next If you do not correspond to the search time */
            if ($search_s_time !== "" && $search_e_time !== "") {
                if ($search_s_time > $send_time || $search_e_time < $send_time) {
                    continue;
                }
            } else if ($search_s_time !== "" && $search_e_time === "") {
                if ($search_s_time > $send_time) {
                    continue;
                }
            } else if ($search_s_time === "" && $search_e_time !== "") {
                if ($search_e_time < $send_time) {
                    continue;
                }
            }

            /* Create the err file path */
            $err_file = $dir . SENDERR;

            /* Check the mailinfo file */
            if ($this->fileExist($err_file) === TRUE) {

                /* Create the envelope file path */
                $envelope = $dir . ENVELOPE . ".bz2";

                /* Check the envelope file */
                if ($this->fileExist($envelope) === TRUE) {
                    $this->readBzline($envelope, $en_num);
                }

                /* Create the start_end file path */
                $start_end = $dir . STARTEND;

                /* Check the start_end file */
                if ($this->fileExist($start_end) === TRUE) {
                    $this->readStartend($start_end, $starttime, $endtime);
                }

                /* Create the send_log file path */
                $send_log = $dir . SENDLOG;

                /* Check the start_end file */
                if ($this->fileExist($send_log) === TRUE) {
                    $this->getLastlog($send_log, $lasttime);
                    $num = count(file($send_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                }

                $status = STATUS_5;

                $data["$disp_dir"]["$starttime"]["$lasttime"]["$status"] = $num . "/" . $en_num . "件";
                continue;

            }

            /* Create the hold file path */
            $hold_file = $dir . SENDHOLD;

            /* Check the hold file */
            if ($this->fileExist($hold_file) === TRUE) {

                /* Create the envelope file path */
                $envelope = $dir . ENVELOPE . ".bz2";

                /* Check the envelope file */
                if ($this->fileExist($envelope) === TRUE) {
                    $this->readBzline($envelope, $en_num);
                }

                /* Create the start_end file path */
                $start_end = $dir . STARTEND;

                /* Check the start_end file */
                if ($this->fileExist($start_end) === TRUE) {
                    $this->readStartend($start_end, $starttime, $endtime);
                } else {
                    $starttime = $disp_date;
                }

                /* Create the send_log file path */
                $send_log = $dir . SENDLOG;

                /* Check the start_end file */
                if ($this->fileExist($send_log) === TRUE) {
                    $this->getLastlog($send_log, $lasttime);
                    $num = count(file($send_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                }

                $status = STATUS_4;

                $data["$disp_dir"]["$starttime"]["$lasttime"]["$status"] = $num . "/" . $en_num . "件";
                continue;

            }
            
            /* Create the lock file path */
            $lock_file = $dir . SENDLOCK;

            /* Check the lock file */
            if ($this->fileExist($lock_file) === TRUE) {
                /* Create the envelope file path */
                $envelope = $dir . ENVELOPE . ".bz2";

                /* Check the envelope file */
                if ($this->fileExist($envelope) === TRUE) {
                    $this->readBzline($envelope, $en_num);
                }

                /* Create the start_end file path */
                $start_end = $dir . STARTEND;

                /* Check the start_end file */
                if ($this->fileExist($start_end) === TRUE) {
                    $this->readStartend($start_end, $starttime, $endtime);
                }

                /* Create the send_log file path */
                $send_log = $dir . SENDLOG;

                /* Check the start_end file */
                if ($this->fileExist($send_log) === TRUE) {
                    $num = count(file($send_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                }

                $status = STATUS_3;

                $data["$disp_dir"]["$starttime"][""]["$status"] = $num . "/" . $en_num . "件";
                continue;
            }

            /* Undelivered */
            /* Create the start_end file path */
            $start_end = $dir . STARTEND;

            /* Check the start_end file */
            if ($this->fileExist($start_end) === FALSE) {

                /* Create the envelope file path */
                $envelope = $dir . ENVELOPE . ".bz2";

                /* Check the envelope file */
                if ($this->fileExist($envelope) === TRUE) {
                    $this->readBzline($envelope, $en_num);
                }

                $status = STATUS_2;

                $data["$disp_dir"]["$disp_date"][""]["$status"] = "0/" . $en_num . "件";
                continue;
            }

            /* Completion */
            /* Create the send_log file path */
            $send_log = $dir . SENDLOG . ".bz2";

            /* Check the send_log file */
            if ($this->fileExist($send_log) === TRUE) {
                $this->readBzline($send_log, $num);
            }

            /* Create the start_end file path */
            $start_end = $dir . STARTEND;

            /* Check the start_end file */
            if ($this->fileExist($start_end) === TRUE) {
                $this->readStartend($start_end, $starttime, $endtime);
            }

            $status = STATUS_1;
            $data["$disp_dir"]["$starttime"]["$endtime"]["$status"] = $num . "件";

        }

        if ($data === array()) {
            return FALSE;
        }

        return $data;

    }

    /************************************************************************
     * method name : readBzline()
     * description : Get the number of rows.
     * args        : $file, $num
     * return      : True or False
     ***********************************************************************/
    public function readBzline($file, &$num)
    {

        $bz_data = "";
        $file_tmp = $file . ".tmp";

        $bzfh = bzopen($file, "r");
        if ($bzfh === FALSE) {
            return FALSE;
        }

        while (!feof($bzfh)) {
            $bz_data .= bzread($bzfh);
        }

        bzclose($bzfh);
        $tmp_fh = fopen($file_tmp, "w");
        if ($tmp_fh === FALSE) {
            return FALSE;
        }

        $ret = fwrite($tmp_fh, $bz_data);
        if ($ret === FALSE) {
            return FALSE;
        }
        fclose($tmp_fh);

        $num = count(file($file_tmp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        $ret = unlink($file_tmp);

        return TRUE;
    }

    /************************************************************************
     * method name : readStartend()
     * description : Get the start_end rows.
     * args        : $file, $start_time, $end_time
     * return      : True or False
     ***********************************************************************/
    public function readStartend($start_end_file, &$start_time = "", &$end_time = "")
    {

        /* Read one file */
        $start_end = file($start_end_file);
        if ($start_end === FALSE) {
            $this->errcode = READ_FILE_FAIL;
            return FALSE;
        }

        if (isset($start_end[0]) === TRUE) {
            $start_time = substr($start_end[0], 0, -4);
        }

        if (isset($start_end[1]) === TRUE) {
            $end_time = substr($start_end[1], 0, -4);
        }

        return TRUE;

    }


    /************************************************************************
     * method name : getLastlog()
     * description : Get the sendlog last time.
     * args        : $file, $last_time
     * return      : True or False
     ***********************************************************************/
    public function getLastlog($logfile, &$lasttime = "")
    {

        $lines = file($logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $last_line = array_pop($lines);
        $column = explode("\t", $last_line, 3);

        if (isset($column[1]) === FALSE) {
            return FALSE;
        }

        $log_day = explode("-", $column[1], 2);
        $year = substr($log_day["0"], 0, 4);
        $mon = substr($log_day["0"], 4, 2);
        $day = substr($log_day["0"], 6, 2);
        $hour = substr($log_day["1"], 0, 2);
        $minutes = substr($log_day["1"], 4, 2);
        $lasttime = $year . "/" . $mon . "/" . $day . " " . $hour . ":" . $minutes;

        return TRUE;
    }
}

/***************************************************************************
 * Class name   : dispLink
 * description  : Class for status
 * property     : $errcode
 **************************************************************************/
Class dispLink extends Validation {

    public $jp_char = "ISO-2022-JP";
    public $utf8_char = "UTF-8";

    /************************************************************************
     * method name : contentConfirm()
     * description : Check the search time.
     * args        : $start_time, $end_time
     * return      : True or False
     ***********************************************************************/
    public function contentConfirm($choice_dir, $page, &$link_content)
    {

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

            if ($dir === $choice_dir) {
                
                if ($page === "content") {

                    /* Create the mailinfo file path */
                    $info_file = STATUS . $dir . INFORAW;

                    /* Check the mailinfo file */
                    if ($this->fileExist($info_file) === FALSE) {
                        throw new SystemWarn(NO_SUCH_FILE, $info_file);
                    }
                    $link_content = $this->readMailinfo($info_file);
                    if ($link_content === FALSE) {
                        throw new SystemWarn(READ_FILE_FAIL, $info_file);
                    }
                    $ret = NULL;
                    return $ret;

                } else if ($page === "list") {

                    /* Create the envelope file path */
                    $envelope_file = STATUS . $dir . ENVELOPE . ".bz2";

                    /* Check the envelope file */
                    if ($this->fileExist($envelope_file) === FALSE) {
                        throw new SystemWarn(NO_SUCH_FILE, $envelope_file);
                    }
                    $ret = $this->readDisp_bz($envelope_file);
                    if ($ret === FALSE) {
                        throw new SystemWarn(READ_FILE_FAIL, $envelope_file);
                    }
                    return $ret;
                } else if ($page === "log") {

                    /* Create the log file path */
                    $log_bz = STATUS . $dir . SENDLOG . ".bz2";
                    $log_file = STATUS . $dir . SENDLOG;

                    /* Check the envelope file */
                    $bz_ret = $this->fileExist($log_bz);
                    $log_ret = $this->fileExist($log_file);
                    if ($bz_ret === TRUE) {
                        $ret = $this->readDisp_bz($log_bz);
                        if ($ret === FALSE) {
                            throw new SystemWarn(READ_FILE_FAIL, $log_bz);
                        }
                        return $ret;
                    }
                    if ($log_ret === TRUE) {
                        $ret = $this->readLog($log_file);
                        if ($ret === FALSE) {
                            throw new SystemWarn(READ_FILE_FAIL, $log_file);
                        }
                        return $ret;
                    }
                    if ($bz_ret === FALSE || $log_ret === FALSE) {
                        throw new SystemWarn(NO_SUCH_FILE, $log_file);
                    }
                }
            }
        }
        throw new SystemWarn(NO_SUCH_JOB);
    }

    /************************************************************************
     * method name : readMailinfo()
     * description : Check the search time.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    public function readMailinfo($file)
    {

        $fh = fopen($file, "r");
        if ($fh === FALSE) {
            return FALSE;
        }

        $num = 0;
        $head = 0;
        $text = "";

        /* Divided into a header and body files mailinfo */
        while (!feof($fh)) {
            if($head === 0) {
                $header["$num"] = fgets($fh);

                $header["$num"] = rtrim($header["$num"]);

                $ret = preg_match('/^To: /', $header["$num"]);
                if ($ret === FALSE) {
                    return FALSE;
                } else if ($ret === 1) {
                    $head_main = explode(" ", $header["$num"], 2);
                    $link_content["from_addr"] = $head_main[1];
                }

                $ret = preg_match('/^From: /', $header["$num"]);
                if ($ret === FALSE) {
                    return FALSE;
                } else if ($ret === 1) {
                    $head_main = explode(" ", $header["$num"], 2);
                    $link_content["to_addr"] = $head_main[1];
                }

                $ret = preg_match('/^Reply-to: /', $header["$num"]);
                if ($ret === FALSE) {
                    return FALSE;
                } else if ($ret === 1) {
                    $head_main = explode(" ", $header["$num"], 2);
                    if (isset($head_main[1]) === TRUE) {
                        $link_content["reply_addr"] = $head_main[1];
                    } else {
                        $link_content["reply_addr"] = "";
                    }
                }

                $ret = preg_match('/^Subject: /', $header["$num"]);
                if ($ret === FALSE) {
                    return FALSE;
                } else if ($ret === 1) {
                    $head_main = explode(" ", $header["$num"], 2);
                    $link_content["subject"] = $head_main[1];
                }

                if (strlen($header["$num"]) === 0) {
                    $head = 1;
                }
            } else {
               $text .= escape_html(fgets($fh)) . "<br>";
            }
            $num++;
        }

        $link_content["text"] = $text;

        return $link_content;

    }

    /************************************************************************
     * method name : readDisp_bz()
     * description : Get the envelope list.
     * args        : $file, $num
     * return      : True or False
     ***********************************************************************/
    public function readDisp_bz($file)
    {

        $bz_data = "";
        $file_tmp = $file . ".tmp";

        $bzfh = bzopen($file, "r");
        if ($bzfh === FALSE) {
            return FALSE;
        }

        while (!feof($bzfh)) {
            $bz_data .= bzread($bzfh);
        }

        bzclose($bzfh);
        $tmp_fh = fopen($file_tmp, "w");
        if ($tmp_fh === FALSE) {
            return FALSE;
        }

        $ret = fwrite($tmp_fh, $bz_data);
        if ($ret === FALSE) {
            return FALSE;
        }
        fclose($tmp_fh);

        $tmp_fh = fopen($file_tmp, "r");
        if ($tmp_fh === FALSE) {
            return FALSE;
        }

        $addr = "";
        while (($line = fgets($tmp_fh)) !== FALSE) {

            $addr .= $line . "<br>";
        }

        $ret = unlink($file_tmp);
        
        return $addr;
    }

    /************************************************************************
     * method name : readLogbz()
     * description : Get the log list.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    public function readLogbz($file)
    {

        $bz_data = "";

        $bzfh = bzopen($file, "r");
        if ($bzfh === FALSE) {
            return FALSE;
        }

        while (!feof($bzfh)) {
            $bz_data .= bzread($bzfh);
        }

        return $bz_data;
    }

    /************************************************************************
     * method name : readLog()
     * description : Get the log list.
     * args        : $file
     * return      : True or False
     ***********************************************************************/
    public function readLog($file)
    {

        $log_data = "";

        $fh = fopen($file, "r");
        if ($fh === FALSE) {
            return FALSE;
        }

        while (!feof($fh)) {
            $log_data .= fgets($fh) . "<br>";
        }

        return $log_data;
    }

}

/***************************************************************************
 * Class name   : alterationJob
 * description  : Class for status
 * property     : $errcode
 **************************************************************************/
Class alterationJob extends Validation {

    /************************************************************************
     * method name : clickBreak()
     * description : break the job.
     * args        : $dir
     * return      : True or False
     ***********************************************************************/
    public function clickBreak($job_dir)
    {

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

        $ret = $this->fileWritable(STATUS);
        if ($ret === FALSE) {
            throw new SystemWarn(NO_WRITE_DIR, STATUS);
        }
        /* Open the derectory */
        $dh = opendir(STATUS);
        if ($dh === FALSE) {
            throw new SystemWarn(NOT_OPEN_DIR, STATUS);
        }

        /* Read the directory */
        while (FALSE !== ($dir = readdir($dh))) {

            if ($dir === $job_dir) {
      
                $dir = STATUS . $dir;

                /* Create the hold file path */
                $hold_file = $dir . SENDHOLD;

                /* Check the hold file */
                if ($this->fileExist($hold_file) === TRUE) {
                    return -2;
                }

                /* Create the hold file path */
                $start_end_file = $dir . STARTEND;

                /* Check the start_end file */
                if ($this->fileExist($start_end_file) === TRUE) {
                    $num = count(file($start_end_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                    if ($num === 2) {
                        return FALSE;
                    }
                }

                /* Create the hold file */
                $ret = touch($hold_file);
                if ($ret === FALSE) {
                    throw new SystemWarn(FAIL_BREAK);
                }
                return TRUE;
            }
        }
        throw new SystemWarn(NO_SUCH_JOB);
    }

    /************************************************************************
     * method name : clickRestart()
     * description : break the job.
     * args        : $dir
     * return      : True or False
     ***********************************************************************/
    public function clickRestart($job_dir)
    {

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

        $ret = $this->fileWritable(STATUS);
        if ($ret === FALSE) {
            throw new SystemWarn(NO_WRITE_DIR, STATUS);
        }

        /* Open the derectory */
        $dh = opendir(STATUS);
        if ($dh === FALSE) {
            throw new SystemWarn(NOT_OPEN_DIR, STATUS);
        }

        /* Read the directory */
        while (FALSE !== ($dir = readdir($dh))) {

            if ($dir === $job_dir) {
      
                $dir = STATUS . $dir;

                /* Create the hold file path */
                $hold_file = $dir . SENDHOLD;

                /* Check the hold file */
                if ($this->fileExist($hold_file) === FALSE) {
                    return FALSE;
                }
                if ($this->fileExist($hold_file) === TRUE) {
                    $ret = unlink($hold_file);
                    if ($ret === FALSE) {
                        throw new SystemWarn(FAIL_RESTART);
                    }
                }
                return TRUE;
            }
        }
        throw new SystemWarn(NO_SUCH_JOB);
    }

    /************************************************************************
     * method name : clickDelete()
     * description : break the job.
     * args        : $dir
     * return      : True or False
     ***********************************************************************/
    public function clickDelete($job_dir)
    {

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

        $ret = $this->fileWritable(STATUS);
        if ($ret === FALSE) {
            throw new SystemWarn(NO_WRITE_DIR, STATUS);
        }

        /* Open the derectory */
        $dh = opendir(STATUS);
        if ($dh === FALSE) {
            throw new SystemWarn(NOT_OPEN_DIR, STATUS);
        }

        /* Read the directory */
        while (FALSE !== ($dir = readdir($dh))) {

            if ($dir === $job_dir) {
            $dir = STATUS . $dir;

                /* Create the lock file path */
                $lock_file = $dir . SENDLOCK;

                /* Check the lock file */
                if ($this->fileExist($lock_file) === TRUE) {
                    return FALSE;
                }

                $dh = opendir($dir);
                while (FALSE !== ($fname = readdir($dh))) {
                    $pos = substr($fname, 0, 1);
                    if ($pos === ".") {
                        continue;
                    }
                    $del_file = $dir . "/" . $fname;

                    if (is_dir($del_file) === TRUE) {
                        throw new SystemWarn(FAIL_DELETE);
                    }

                    $ret = unlink($del_file);
                    if ($ret === FALSE) {
                        throw new SystemWarn(FAIL_DELETE);
                    }
                }
                $ret = rmdir($dir);
                if ($ret === FALSE) {
                    throw new SystemWarn(FAIL_DELETE);
                }
                return TRUE;
            }
        }
        throw new SystemWarn(NO_SUCH_JOB);
    }

}

?>
