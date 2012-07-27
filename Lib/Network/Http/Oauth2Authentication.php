<?php
class Oauth2Authentication {
	
	public static function authentication(HttpSocket $http, &$authInfo) {
		$http->request['header']['Content-Type'] = 'Application/json';
	}

}
?>