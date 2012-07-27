<?php
$ZendLibrary = App::pluginPath('YouTube') . DS . 'Vendor';
set_include_path(get_include_path() . PATH_SEPARATOR . $ZendLibrary);
require_once "Zend/Loader.php";
Zend_Loader::loadClass('Zend_Gdata_YouTube');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
$access = array(
	'client_id' => '1002534905775.apps.googleusercontent.com',
	'client_secret' => 'HNGkR3LiMhvDAJkAp5PXrO3w',
	'authComponent' => 'UserAuth',
	'userClass' => 'Usermgmt.User',
	'userModel' => 'User'
);
Configure::write('YouTube', $access);
?>