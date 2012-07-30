<?php
App::uses('HttpSocket', 'Network/Http');
App::uses('HttpSocket', 'Network/Http');
App::uses('CakeException', 'Cake/Error');

class YouTubeDataAPIRequestException extends CakeException {
	protected $_messageTemplate = 'An error occured : %s';
}

class YouTubeDataAPIUnauthorizedException extends CakeException {
	protected $_messageTemplate = 'An error occured : %s';
}

class YouTubeDataAPI {
	
	const GOOGLE_GRANT_ACCESS_URL = 'https://accounts.google.com/o/oauth2/auth';
	const GOOGLE_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
	const YOUTUBE_SCOPE = 'https://gdata.youtube.com';
	const USER_VIDEOS_URL = 'https://gdata.youtube.com/feeds/api/users/:user/uploads';
	const USER_VIDEO_URL = 'https://gdata.youtube.com/feeds/api/users/:user/uploads/:videoid?v=2';
	const VIDEO_URL = 'https://gdata.youtube.com/feeds/api/videos/:videoid?v=2';
	const CURRENT_LOGIN_USER = 'default';
	const GRANT_TOKEN = 'authorization_code';
	const GRANT_REFRESH = 'refresh_token';

	public static function getGrantAccessURL($clientID, $redirectURI, $accessType) {
		return self::_buildURL(
			self::GOOGLE_GRANT_ACCESS_URL, 
			array(
				'client_id' => $clientID,
				'redirect_uri' => $redirectURI,
				'scope' => self::YOUTUBE_SCOPE,
				'response_type' => 'code',
				'access_type' => $accessType
			)
		);
	}

	public static function getAccessToken($code, $clientID, $clientSecret, $redirectURI, $grantType = self::GRANT_TOKEN, $refreshToken = null) {
		$HttpSocket = new HttpSocket();
		$params = array(
			'client_id' => $clientID,
			'client_secret' => $clientSecret,
			'grant_type' => $grantType,
		);
		if($code != null) {
			$params['code'] = $code;
		}
		if($redirectURI != null) {
			$params['redirect_uri'] = $redirectURI;
		}
		if($refreshToken != null) {
			$params['refresh_token'] = $refreshToken;
		}
		$results = $HttpSocket->post(self::GOOGLE_TOKEN_URL, $params, array('redirect' => true));
		if(!$results->isOK()) {
			debug($HttpSocket->request);
			throw new YouTubeDataAPIRequestException('Server responded with error status ' . $results->code . ' : ' . $results->reasonPhrase);
		}
		return $results->body();
	}

	public static function getUserVideos($api, $location, $user, $accessToken = null) {
		$url = String::insert($location, compact('user'));
		$url = ($accessToken == null) ? $url : $url . '?access_token=' . $accessToken;
		return $api->getVideoFeed($url);
	}

	public static function getVideo($api, $videoID, $accessToken) {
		$accessToken = null;
		$url = ($accessToken == null) ? String::insert(self::VIDEO_URL, array('videoid' => $videoID)) : String::insert(self::USER_VIDEO_URL, array(
			'user' => self::CURRENT_LOGIN_USER,
			'videoid' => $videoID
		)) . '&access_token=' . $accessToken;
		return $api->getVideoEntry(null, $url);
	}

	public static function refreshToken(Zend_Http_Response $response, $authData, $clientID, $clientSecret) {
		if($response->getStatus() != 401) {
			return $authData;
		}
		else {
			$data = json_decode(self::getAccessToken(null, $clientID, $clientSecret, null, self::GRANT_REFRESH, $authData['refresh_token']));
			return array(
				'access_token' => $data->access_token,
				'refresh_token' => $authData['refresh_token']
			);
		}
	}

	private static function _buildURL($url, $params) {
		$paramsList = empty($params) ? '' : '?';
		foreach($params as $p => $v) {
			$paramsList .= $p . '=' . $v . '&';
		}
		$paramsList = rtrim($paramsList, '&');
		return $url . $paramsList;
	}

}

?>