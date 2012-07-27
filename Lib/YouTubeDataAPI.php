<?php

class YouTubeDataAPI {
	
	const GOOGLE_GRANT_ACCESS_URL = 'https://accounts.google.com/o/oauth2/auth';
	const GOOGLE_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
	const YOUTUBE_SCOPE = 'https://gdata.youtube.com';

	public static function getGrantAccessURL($clientID, $redirectURI, $accessType) {
		return $this->_buildURL(
			self::GOOGLE_GRANT_ACCESS_URL, 
			array(
				'client_id' => $clientID,
				'redirect_uri' => $redirectURI,
				'scope' => self::YOUTUBE_SCOPE,
				'response_type' => 'code',
				'access_type' => $accessType
			)
		)
	}

	private function _buildURL($url, $params) {
		$paramsList = empty($params) ? '' : '?';
		foreach($params as $p => $v) {
			$paramsList .= $p . '=' . $v . '&';
		}
		$paramsList = rtrim($paramsList, '&');
		return $url . $paramsList;
	}

}

?>