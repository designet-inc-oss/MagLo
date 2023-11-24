<?php
/***************************************************************************
 * Class for template and html
 **************************************************************************/
require_once('../vender/Smarty/libs/Smarty.class.php');
include_once("../lib/dglibescape.php");
define("SUFFIX", ".tmpl");

/***************************************************************************
 * Class name   : Display
 * description  : Class for template and html
 * property     : $tags    
 *              : $tmplfile
 *              : $smarty
 **************************************************************************/
Class Display {
    public  $tags = array();
    protected $tmplfile = "";
    protected $smtarty;

    /************************************************************************
     * method name : __construct()
     * description : Initialize Smarty property
     * args        : None
     * return      : None
     ***********************************************************************/
    public function __construct()
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir('../tmpl/');
        $this->smarty->setCompileDir('../tmpl/compile/');
        $this->smarty->setCacheDir('../tmpl/cache/');
        $this->smarty->setConfigDir('../etc/config/');
        $this->tmplfile = basename($_SERVER["PHP_SELF"],".php"). SUFFIX;
        $this->tags["owncss"] = basename($_SERVER["PHP_SELF"],".php"). ".css";
    }

    /************************************************************************
     * method name : view()
     * description : Display html tags
     * args        : None
     * return      : None
     ***********************************************************************/
    public function view() {
        $this->_existsTmpl($this->tmplfile);

        $this->__save();
        $this->_register();

        $res = $this->smarty->display($this->tmplfile);
    }

    /************************************************************************
     * method name : _register
     * description : registered in the Smarty object and value replacement tag
     * args        : None
     * return      : None
     ***********************************************************************/
    protected function _register()
    {
        foreach ($this->tags as $key => $val) {
            $this->smarty->assign($key, $val); 
        }
    }

    /************************************************************************
     * method name : _existsTmpl()
     * description : Check for the existence of the template file
     * args        : None
     * return      : None
     ***********************************************************************/
    protected function _existsTmpl() {
        $res = $this->smarty->templateExists($this->tmplfile);
        if ($res === FALSE) {
            throw new SystemCrit(TMP_FILE_ERROR, $this->tmplfile);
        }
    }

    /************************************************************************
     * method name : __save()
     * description : Stores the value of the holding
     * args        : None
     * return      : None
     ***********************************************************************/
    private function __save() {
        foreach ($_POST as $name => $value) {
            if (isset($_POST["$name"])) {
                $ch_value = escape_html($value);
                $this->tags["save_$name"] = $ch_value;
            }
        }
    }

    /************************************************************************
     * method name : viewErr()
     * description : output the HTML for the system error
     * args        : None
     * return      : None
     ***********************************************************************/
    public function viewErr($output)
    {
        $html = <<<EOD
<HTML>
<title>システムエラー</title>

<link href="./style.css" rel="stylesheet" type="text/css">
<div id="text1">システムエラー画面</div>

</HTML>
EOD;
         echo($html);
    }
}

?>
