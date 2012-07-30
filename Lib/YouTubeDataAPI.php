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
	const USER_VIDEO_URL = 'https://gdata.youtube.com/feeds/api/users/:user/uploads';
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

	public static function getAccessToken($code, $clientID, $clientSecret, $redirectURI) {
		$HttpSocket = new HttpSocket();
		$results = $HttpSocket->post(self::GOOGLE_TOKEN_URL, array(
			'code' => $code,
			'client_id' => $clientID,
			'client_secret' => $clientSecret,
			'redirect_uri' => $redirectURI,
			'grant_type' => self::GRANT_TOKEN,
		), array('redirect' => true));
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
		$url = String::insert($videoID, array('videoid' => $videoID));
		$url = ($accessToken == null) ? $url : $url . '&access_token=' . $accessToken;
		return ($accessToken == null) ? $api->getVideoEntry($videoID) : $api->getFullVideoEntry($url);
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