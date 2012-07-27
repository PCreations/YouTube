<?php
App::uses('YouTubeDataAPI', 'YouTube.Lib');

class YouTubeDataAPIComponent extends Component {
	
	public function getGrantAccessURL($clientID, $redirectURI, $accessType) {
		return YouTubeDataAPI::getGrantAccessUrl($clientID, $redirectURI, $accessType);
	}

	public function getAccessToken($code, $clientID, $clientSecret, $redirectURI) {
		return YouTubeDataAPI::getAccessToken($code, $clientID, $clientSecret, $redirectURI);
	}

}

?>