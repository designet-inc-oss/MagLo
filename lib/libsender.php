<?php

/*************************************************************************
 * read_sender_list(&$data, $filepath)
 *
 * [function]
 *   Reads the contents of a file path that was passed as an argument, 
 *   to be stored in data.
 *
 * [Argument]
 *   &$data        Array for storing the contents of the file to read.
 *   $filepath     The path of the file to be read.
 * [return]
 *   TRUE          Success.
 *   FALSE         Failure.
 ************************************************************************/
include_once("../lib/dglibescape.php");


function read_sender_list(&$data, $filepath)
{
    $validate = new Validation();

    /* Check sender_list exitst. */
    $ret = $validate->fileExist($filepath);
    if ($ret === FALSE) {
        throw new SystemWarn(NO_SUCH_FILE, $filepath);
    }

    /* Check sender_list readable. */
    $ret = $validate->fileReadable($filepath);
    if ($ret === FALSE) {
        throw new SystemWarn(NO_READ_FILE, $filepath);
    }

    /* Open the file in the path that was passed as an argument. */
    $fh = fopen($filepath, "r");
    if ($fh === FALSE) {
        throw new SystemWarn(READ_SENDER_ERROR, $filepath);
    }

    for ($i = 0; !(feof($fh)); $i++) {
        /* Read one line the contents of a file. */
        $line = fgets($fh);
        if ($line === FALSE) {
            break;
        }

        /* Remove the line break code. */
        $trim_line = rtrim($line, "\n");

        /* Split the tab contents. */
        $sender = explode("\t", $trim_line);
        /* The number of elements in the array is not equal to 2,
         * format abnormal. */
        if (count($sender) !== 2) {
            throw new SystemWarn(SENDER_FORM_INVALID, $filepath);
        }

        /* Set the hash value. */
        $hash = md5($sender[0] . $sender[1]);

        /* HTML Special Chars. */
        $name = escape_html($sender[0]);
        $addr = escape_html($sender[1]);

        /* Store the sequence data obtained. */
        $data[$i][$hash][$name] = $addr;
    }

    return TRUE;
}

/*************************************************************************
 * alter_sender_list($filepath, $status, $line_num, $hash, $name, $addr)
 *
 * [function]
 *   To be add, updated or deleted by the status value,
 *   the information of sender_list specified.
 *
 * [Argument]
 *   $filepath     The path of the file to be read.
 *   $stauts       0: Add.
 *                 1: Update.
 *                 2: Delete.
 *   $line_num     Line number to be processed.
 *   $hash         Hash value.
 *   $name         Sender name.
 *   $addr         Sender address.
 * [return]
 *   TRUE          Success.
 *   FALSE         Failure.
 ************************************************************************/

function alter_sender_list($filepath, $status, $line_num, $hash, $name, $addr)
{
    /* Specify the path to the file for writing. */
    $pid = getmypid();
    $time = time();
    $tmp = "$filepath.tmp.$pid.$time";

    /* To read the contents of the file, it is stored in the array. */
    $ret = read_sender_list($data, $filepath);
    if ($ret === FALSE) {
        throw new SystemWarn(READ_SENDER_ERROR, $filepath);
    }


    /* Check the line number and hash value. */
    if ($status !== 0) {

        /* Count the number of elements in the array. */
        if (count($data) === 0) {
            throw new SystemWarn(NOT_EXIST_SENDER, $filepath);
        }

        $ret = isset($data[$line_num][$hash]);
        if ($ret === FALSE) {
            throw new SystemWarn(NOT_EXIST_SENDER, $filepath);
        }
    }

    $validate = new Validation();

    /* Check temporary file writable. */
    $ret = $validate->fileWritable(dirname($tmp));
    if ($ret === FALSE) {
        throw new SystemWarn($validate->errcode, $tmp);
    }

    /* Open the file for writing.*/
    $fh = fopen($tmp, "w");
    if ($fh === FALSE) {
        throw new SystemWarn(NO_WRITE_FILE, $filepath);
    }

    /* Put an exclusive lock for writing files */
    $ret = flock($fh, LOCK_EX);
    if ($ret === FALSE) {
        fclose($fh);
        /* Delete template file. */
        unlink($tmp);
        throw new SystemWarn(LOCK_FILE_FAIL, $filepath);
    }

    if (isset($data)) { 
        foreach ($data as $line_key => $hash_value) {
            if ($line_num === (string) $line_key) {
                /* Modify process */
                if ($status === 1) {
                    $sender = $name . "\t" . $addr . "\n";
                /* Delete process */
                } else if ($status === 2) {
                    continue;
                }
            } else {
                $hash_key = key($hash_value);
                $name_key = key($hash_value[$hash_key]);
                $prim_name = htmlspecialchars_decode($name_key);
                $prim_addr = htmlspecialchars_decode($data[$line_key][$hash_key][$name_key]);
                $sender = $prim_name . "\t" . $prim_addr . "\n";
            }

            /* Update the contents of the sender list file. */
            $ret = fwrite($fh, $sender);
            if ($ret === FALSE) {
                /* Unlock template file. */
                $ret = flock($fh, LOCK_UN);
                if ($ret === FALSE) {
                    fclose($fh);
                    /* Delete template file. */
                    unlink($tmp);
                    throw new SystemWarn(UNLOCK_FILE_FAIL, $filepath);
                }
            }
        }
    }

    /* Add process. */
    if ($status === 0) {
        $sender = $name . "\t" . $addr . "\n";

        /* Add to the sender list file. */
        $ret = fwrite($fh, $sender);
        if ($ret === FALSE) {
            /* Unlock template file. */
            $ret = flock($fh, LOCK_UN);
            if ($ret === FALSE) {
                fclose($fh);
                /* Delete template file. */
                unlink($tmp);
                throw new SystemWarn(UNLOCK_FILE_FAIL, $filepath);
            }
        }
    }

    /* Unlock the file. */
    $ret = flock($fh, LOCK_UN);
    if ($ret === FALSE) {
        fclose($fh);
        /* Delete template file. */
        unlink($tmp);
        throw new SystemWarn(UNLOCK_FILE_FAIL, $filepath);
    }

    /* Rename the file name sender_list. */
    $ret = rename($tmp, $filepath);
    if ($ret === FALSE) {
        fclose($fh);
        /* Delete template file. */
        unlink($tmp);
        throw new SystemWarn(RENAME_FILE_FAIL, $filepath);
    }

    fclose($fh);

    return TRUE;
}

?>
