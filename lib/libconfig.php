<?php

class Config extends Validation {
    private $file = CONFIG;
    public $conf = array();

    public function __construct()
    {
        global $conf;

        $ret = $this->__readConfig();
        if ($ret === FALSE) {
            throw new SystemWarn($this->errcode, $this->errargs);
        }

        $ret = $this->__validateConfig();
        if ($ret === FALSE) {
            throw new SystemWarn($this->errcode, $this->errargs);
        }

        $conf = $this->conf;
    }

    private function __readConfig()
    {

        /* Presence check */
        $ret = $this->fileExist($this->file);
        if ($ret === FALSE) {
            $this->errargs = array($this->file);
            return FALSE;
        }

        $ret = $this->fileReadable($this->file);
        if ($ret === FALSE) {
            $this->errcode = CONFIG_READ_ERROR;
            $this->errargs = array($this->file);
            return FALSE;
        }

        $this->conf = @parse_ini_file($this->file);
        if ($this->conf === FALSE) {
            $this->errcode = CONFIG_SYNTAX_ERROR;
            $this->errargs = array($this->file);
            return FALSE;
        }

        return TRUE;
    }
    
    private function __validateConfig()
    {
        global $confRules;

        foreach ($confRules as $key => $dummy) {
            if (!isset($this->conf[$key]) &&
                $confRules[$key]["default"] !== "") {
                $this->conf[$key] = $confRules[$key]["default"];
            }

            if (!isset($this->conf[$key])) {
                $this->errcode = INVALID_CONFIGURATION;
                $this->errargs = array($key);
                return FALSE;
            }
        }

        foreach ($this->conf as $name => $value) {
            $args = array();

            if (!isset($confRules[$name])) {
                $this->errcode = CONFIG_SYNTAX_ERROR;
                $this->errargs = array($this->file);
                return FALSE;
            }

            if (method_exists($this, $confRules[$name]["method"]) === FALSE) {
                continue;
            }

            $args[] = $value;

            if ($confRules[$name]["addargs"] !== "") {
                $args[] = $confRules[$name]["addargs"];
            }

            $ret = call_user_func_array(array($this, $confRules[$name]["method"]), $args);
            if ($ret === FALSE) {
                $this->errcode = $confRules[$name]["errcode"];
                $this->errargs = array($this->file);
                return FALSE;
            }
        }

        return TRUE;    
    }
}

?>
