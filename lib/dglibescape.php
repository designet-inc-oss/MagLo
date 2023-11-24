<?php

/*********************************************************
 * escape_html()
 *
 * Adaptation to noncompatible deviation of htmlspecialchars() from PHP 5.3 to PHP5.4
 *
 * [return value]
 *       HTML escaped string of $str
 **********************************************************/
function escape_html($str, $flags=ENT_COMPAT, $encoding = "UTF-8") 
{
    return htmlspecialchars($str, $flags, $encoding);
}
?>
