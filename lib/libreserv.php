<?php

/***************************************************************************
 * Class name   : reserv
 * description  : Function used in the distribution reservation screen.
 * property     : 
 **************************************************************************/
Class reserv extends Validation {

    public $tname = "";
    public $taddr = "";
    public $fname = "";
    public $faddr = "";
    public $reply_to = "";
    public $subject = "";
    public $text = "";
    public $date = "";
    public $control_num = 0;
    public $to_char = "ISO-2022-JP";
    public $from_char = "UTF-8";
    private $encoding = "B";
    private $test_send = FALSE;
    private $immd_send = FALSE;
    private $rsrv_send = FALSE;
    private $temporal_charset = "UTF-8";

    /************************************************************************
     * method name : __construct()
     * description : Initialize reserv value
     * args        : $to, $from, $reply_to, $subject, $text
     * return      : None
     ***********************************************************************/
    public function __construct()
    {

        $this->reply_to = $_POST["replyaddr"];
        $this->subject = $_POST["subject"];
        $this->text = $_POST["maintext"];
        if (isset($_POST["test_send"])) {
            $this->test_send = TRUE;
            $this->date = date("Y/m/d H:i:s", time());
        }
        if (isset($_POST["immd_send"])) {
            $this->immd_send = TRUE;
            $this->date = date("Y/m/d H:i:s", time());
        }
        if (isset($_POST["rsrv_send"])) {
            $this->rsrv_send = TRUE;
            $this->date = $_POST["date"];
        }

    }

    /************************************************************************
     * method name : changeMimeencode()
     * description : Mime encoded string.
     * args        : $word
     * return      : $mime_word
     ***********************************************************************/
    public function changeMimeencode($word)
    {

        /* Check internal encoding */
        $default_internal_encode = mb_internal_encoding();
        if ($default_internal_encode !== $this->temporal_charset) {
            /* Change internal encoding 'UTF-8' temporally */
            mb_internal_encoding($this->temporal_charset);
        }

        /* Unify input encoding of the string as UTF-8 */
        $en_word = mb_convert_encoding($word, $this->temporal_charset, $this->from_char);

        /* Mime encode with internal encoding 'UTF-8'
         * Note: charset of $en_word must be same as the internal encoding.
         */
        $mime_word = mb_encode_mimeheader($en_word, $this->to_char, $this->encoding);

        /* Reset default internal encode */
        mb_internal_encoding($default_internal_encode);

        return $mime_word;
    }

    /************************************************************************
     * method name : makeSendtime()
     * description : Create a sendtime file from 
     *               the input value of the reservation screen.
     * args        : 
     * return      : True
     ***********************************************************************/
    public function makeSendtime()
    {

        $file = STATUS . $this->control_num . SENDTIME;

        $rsrv_date = explode(' ', $this->date);
        $YMD = explode('/', $rsrv_date[0]);
        $hm = explode(':', $rsrv_date[1]);

        if (count($hm) === 2) {
            $hm[2] = "00";
        }

        /* Make sendtime date */
        $sendtime = $YMD[0] . $YMD[1] . $YMD[2] . $hm[0] . $hm[1] . $hm[2] . "\r\n";
        $sendtime .= $_SESSION["user"] . "\r\n";

        /* Make the sendtime file */
        $ret = $this->makeFile($file, $sendtime);

        return TRUE;

    }

    /************************************************************************
     * method name : makeMailinfo()
     * description : Create a mailinfo file from 
     *               the input value of the reservation screen.
     * args        : 
     * return      : True
     ***********************************************************************/
    public function makeMailinfo()
    {

        $file = STATUS . $this->control_num . MAILINFO;

        /* call the function of mime-encoding */
        if ($this->tname !== "") {
            $mime_tname = $this->changeMimeencode($this->tname);
        }
        if ($this->fname !== "") {
            $mime_fname = $this->changeMimeencode($this->fname);
        }
        $subject = $this->changeMimeencode($this->subject);

        /* encode the body */
        $text = mb_convert_encoding($this->text, $this->to_char, $this->from_char);

        /* Set to address */
        if ($this->tname === "") {
            $to = $this->taddr;
        } else {
            $to = $mime_tname . '<' . $this->taddr . '>';
        }

        /* Set from address */
        if ($this->fname === "") {
            $from = $this->faddr;
        } else {
            $from = $mime_fname . '<' . $this->faddr . '>';
        }

        /* is stored in a variable which has been encoded mime */
        $mailinfo = "To: $to\r\n";
        $mailinfo .= "From: $from\r\n";
        if (isset($this->reply_to)) {
             $mailinfo .= "Reply-to: $this->reply_to\r\n";
        }
        $mailinfo .= "Subject: $subject\r\n";
        $mailinfo .= "Content-Type: text/plain; charset=ISO-2022-JP\r\n" .
                     "Content-Transfer-Encoding: 7bit\r\n";
        $mailinfo .= "\r\n";
        $mailinfo .= "$text\r\n";

        /* Make the mailinfo file */
        $ret = $this->makeFile($file, $mailinfo);

        /* Make raw date */
        /* Set to address */
        if ($this->tname === "") {
            $to = $this->taddr;
        } else {
            $to = $this->tname . '<' . $this->taddr . '>';
        }

        /* Set from address */
        if ($this->fname === "") {
            $from = $this->faddr;
        } else {
            $from = $this->fname . '<' . $this->faddr . '>';
        }

        /* is stored in a variable which has been encoded mime */
        $mailinfo = "";
        $mailinfo = "To: $to\r\n";
        $mailinfo .= "From: $from\r\n";
        if (isset($this->reply_to)) {
             $mailinfo .= "Reply-to: $this->reply_to\r\n";
        }
        $mailinfo .= "Subject: $this->subject\r\n";
        $mailinfo .= "Content-Type: text/plain; charset=ISO-2022-JP\r\n" .
                     "Content-Transfer-Encoding: 7bit\r\n";
        $mailinfo .= "\r\n";
        $mailinfo .= "$this->text\r\n";

        /* Make the mailinfo file */
        $ret = $this->makeFile("$file.raw", $mailinfo);

        return TRUE;

    }

    /************************************************************************
     * method name : makeFile()
     * description : Create the file from the argument.
     * args        : $file, $data
     * return      : True
     ***********************************************************************/
    public function makeFile($file, $data)
    {

        /* Make the temporary file name*/
        $tmp_file = $file . time();

        /* Open the temporary file */
        $fh = fopen($tmp_file, "w");
        if ($fh === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(OPEN_FILE_FAIL, $file);
        }

        /* Write the data */ 
        $ret = fwrite($fh, $data);
        if ($ret === FALSE) {
            fclose($fh);
            deleteDir($this->control_num);
            throw new SystemWarn(NO_WRITE_FILE, $file);
        }

        /* unlock and close the start-end_time file */ 
        fclose($fh);

        /* Move the file */
        $ret = rename($tmp_file, $file);
        if ($ret === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(RENAME_FILE_FAIL, $file);
        }

        return TRUE;

    }

    /************************************************************************
     * method name : makeEnvelope_file()
     * description : Create a mailinfo file from 
     *               the input value of the reservation screen.
     * args        : 
     * return      : True
     ***********************************************************************/
    public function makeEnvelope_file()
    {

        /* Create file name */
        $file = STATUS . $this->control_num . ENVELOPE;

        /* */
        if ($this->test_send !== FALSE) {
            $fh = fopen("$file", "w");
            if ($fh === FALSE) {
                deleteDir($this->control_num);
                throw new SystemWarn(OPEN_FILE_FAIL, $file);
            }

            $enve_to = $_POST["testaddr"] . "\r\n";
            $ret = fwrite($fh, $enve_to);
            if ($ret === FALSE) {
                fclose($fh);
                deleteDir($this->control_num);
                throw new SystemWarn(WRITE_FILE_FAIL);
            }

            /* Make bz2 file */
            $this->makeBz2_file($file);

            fclose($fh);
            return TRUE;
        }

        /* Error if there is no file */
        if (isset($_FILES['upfile']['tmp_name']) === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(READ_EVNELOPE_FAIL, $file);
        }

        /* Get the upload file */
        if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {

            /* Check the file size */
            if(filesize($_FILES['upfile']['tmp_name']) === 0) {
                deleteDir($this->control_num);
                throw new SystemWarn(UPFILE_EMPTY, $file);
            }

            /* Open the file */
            $fh = fopen($_FILES['upfile']['tmp_name'], "r");
            if ($fh === FALSE) {
                deleteDir($this->control_num);
                throw new SystemWarn(OPEN_FILE_FAIL, $file);
            }

            /* Lock the file */
            $ret = flock($fh, LOCK_EX);
            if ($ret === FALSE) {
                fclose($fh);
                deleteDir($this->control_num);
                throw new SystemWarn(LOCK_FILE_FAIL, $file);
            }

            /* Initialize the variables */
            $num = 0;
            $disp_mess = "";
            $mess = array();
            
            /* Read one line */
            while (($line = fgets($fh)) !== FALSE) {
                /* Increase the number of lines */
                $num++;

                $line = rtrim($line);

                /* Skip blank line */
                if (strlen($line) === 0) {
                    continue;
                }

                /* Separated by tab */
                $column = explode("\t", $line);
                if ($column === FALSE) {
                    $mess[] = $this->makeMessage(INVALID_UPFILE_FORMAT, $num);
                    continue;
                }

                /* Check the number of columns */
                $num_column = count($column);
                if ($num_column <= 0 || $num_column > 6) {
                    $mess[] = $this->makeMessage(INVALID_UPFILE_FORMAT, $num);
                    continue;
                }

                /* Check the bytes of columns */
                foreach ($column as $value) {
                    if (strlen($value) > 512) {
                        $mess[] = $this->makeMessage(INVALID_UPFILE_FORMAT, $num);
                        continue 2;
                    }
                }

                /* Check the e-mail address */
                $column[0] = trim($column[0]);

                $ret = $this->email($column[0]);
                if ($ret === FALSE) {
                    $mess[] = $this->makeMessage(INVALID_UPFILE_MAIL, $num);
                    continue;
                }

                /* Check the e-mail address length */
                $ret = $this->ge($column[0], 5);
                if ($ret === FALSE) {
                    $mess[] = $this->makeMessage(INVALID_UPFILE_MAIL, $num);
                    continue;
                }

                $ret = $this->le($column[0], 256);
                if ($ret === FALSE) {
                    $mess[] = $this->makeMessage(INVALID_UPFILE_MAIL, $num);
                    continue;
                }

            }

            if ($mess !== array()) {
                $disp_mess = implode("<br>", $mess);
                deleteDir($this->control_num);
                throw new SystemWarn(DISPLAYED_TOGETHER, $disp_mess);
            }
 
            /* Move file */
            $ret = move_uploaded_file($_FILES['upfile']['tmp_name'], $file);
            if ($ret === FALSE) {
                fclose($fh);
                deleteDir($this->control_num);
                throw new SystemWarn(READ_EVNELOPE_FAIL, $file);
            }

            /* Make bz2 file */
            $this->makeBz2_file($file);

        } else {
            deleteDir($this->control_num);
            throw new SystemWarn(NOT_UPLOADED);
        }

        fclose($fh);
        return TRUE;

    }

    /************************************************************************
     * method name : makeBz2_file()
     * description : Create a bz2 file from 
     *               the input value of the reservation screen.
     * args        : $file
     * return      : True
     ***********************************************************************/
    public function makeBz2_file($file)
    {

        /* Create a name for the extension bz2 format */
        $bz2_ext = $file . ".bz2";

        /* Creates a temporary file */
        $pid = getmypid();
        $tmp_file = "../var/bz_tmp" . $pid . $this->control_num;

        /* Open the file to be converted into a bz2 file */
        $fh = fopen($file, "r");
        if ($fh === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(OPEN_FILE_FAIL, $file);
        }

        /* open a file in a format bz2 primary file */
        $bz_fh = bzopen($tmp_file, "w");
        if ($bz_fh === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(NO_WRITE_FILE, $file);
        }

        /* Read one line */
        while (($line = fgets($fh)) !== FALSE) {

            /* Write bz2 file */
            $ret = bzwrite($bz_fh, $line);
            if ($ret === FALSE) {
                deleteDir($this->control_num);
                throw new SystemWarn(WRITE_FILE_FAIL, $file);
            }

        }

        /* Close the file */
        fclose($fh);
        bzclose($bz_fh);

        /* Move the file */
        $ret = rename($tmp_file, $bz2_ext);
        if ($ret === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(RENAME_FILE_FAIL, $file);
        }

        /* Delete the file */
        $ret = unlink($file);
        if ($ret === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(DELETE_FILE_FAIL, $file);
        }

        return TRUE;

    }

    /************************************************************************
     * method name : makeControlnum()
     * description : Create the control num.
     * args        : 
     * return      : $control_num or False
     ***********************************************************************/
    public function makeControlnum()
    {

        /* Create the control num */
        $control_num = ceil(microtime(TRUE) * 1000);

        /* Create the directory path */
        $control_dir = STATUS . $control_num;

        /* Check of the directory */
        $ret = is_dir($control_dir);
        if ($ret === TRUE) {

            /* Create the control num, second time */
            $control_num = ceil(microtime(TRUE) * 1000);

            /* Create the directory path again*/
            $control_dir = STATUS . $control_num;

            /* Check of the directory, second time */
            if(is_dir($control_dir) === TRUE) {
                throw new SystemWarn(CONTROL_DUPLICATED, $control_num);
            }
        }

        /* Create the directory */
        $ret = mkdir($control_dir);
        if ($ret === FALSE) {
            throw new SystemWarn(FAIL_CREATE_DIR, $control_dir);
        }

        return $control_num;
    }


    /**********************************************************************
     * METHOD NAME：makeMessage()
     * USE        ：Create a message in order to connect.
     * ARGUMENT   ：
     * RETURN     ：$disp_mess
     **********************************************************************/
    public function makeMessage($code, $replace)
    {
        global $msg;

        /* Return FALSE if there is no message code */
        if (!isset($code)) {
            return "";
        }

        /* Shaped into a error message */
        $disp_mess = vsprintf($msg[LANG][$code]["web"], $replace);

        /* Returns a message that is formatted */
        return $disp_mess;
    }


    /**********************************************************************
     * METHOD NAME：makeFlagfile()
     * USE        ：Create a flagfile of test or immediate delivery.
     * ARGUMENT   ：
     * RETURN     ：TRUE
     **********************************************************************/
    public function makeFlagfile()
    {

        /* Preparate test delivery */
        if ($this->test_send === TRUE) {
            $file = STATUS . $this->control_num . SENDTEST;

        /* Preparate immediate delivery */
        } else if ($this->immd_send === TRUE) {
            $file = STATUS . $this->control_num . SENDIMMD;
        }

        /* Make flagfile */
        $ret = touch($file);
        if ($ret === FALSE) {
            deleteDir($this->control_num);
            throw new SystemWarn(CREATE_FILE_FAIL);
        }
    }

}

function deleteDir($control_num)
{
    /* Initialize */
    $dir_path = STATUS . $control_num . '/';
    $files = array();

    /* */
    if (is_dir($dir_path) === FALSE) {
        throw new SystemWarn(NO_SUCH_DIR, $dir_path);
    }

    /* Open directory */
    $dh = @opendir($dir_path);
    if ($dh === FALSE) {
        throw new SystemWarn(NOT_OPEN_DIR, $dir_path);
    }

    while (($entry = readdir($dh)) !== FALSE) {
        if ($entry === '.' or $entry === '..') {
            continue;
        }

        if (is_dir($entry) === TRUE) {
            continue;
        }

        $ret = unlink("$dir_path$entry");
        if ($ret === FALSE) {
            continue;
        }
    }

    $ret = @rmdir($dir_path);
    if ($ret === FALSE) {
        closedir($dh);
        throw new SystemWarn(FAIL_DELETE_DIR, $dir_path);
    }

    closedir($dh);
    return TRUE;
}


/***************************************************************************
 * Class name   : prepTestdeliver
 * description  : Function used in the distribution reservation screen.
 * property     : 
 **************************************************************************/
Class prepTestdeliver extends reserv {

    /************************************************************************
     * method name : makeEnvelope_file()
     * description : Create envelope_to_list file 
     * args        : 
     * return      : True
     ***********************************************************************/
    public function makeEnvelope_file()
    {
        /* Create file path */
        $file = STATUS . $this->control_num . ENVELOPE;

        /* Make envelope_to_list file */
        $fh = fopen("$file", "w");
        if ($fh === FALSE) { 
            deleteDir($this->control_num);
            throw new SystemWarn(OPEN_FILE_FAIL, $file);
        }       
 
        /* Write e-mail address on file */
        $enve_to = $_POST["testaddr"] . "\r\n";
        $ret = fwrite($fh, $enve_to);
        if ($ret === FALSE) {
            fclose($fh);
            deleteDir($this->control_num);
            throw new SystemWarn(WRITE_FILE_FAIL);
        }       
 
        /* Make bz2 file */
        $this->makeBz2_file($file);
 
        fclose($fh);
        return TRUE;
    }
}

?>
