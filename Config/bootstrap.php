<?php
$ZendLibrary = App::pluginPath('YouTube') . DS . 'Vendor';
set_include_path(get_include_path() . PATH_SEPARATOR . $ZendLibrary);
require_once "Zend/Loader.php";
?>