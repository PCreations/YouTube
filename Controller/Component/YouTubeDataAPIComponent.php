<?php
App::uses('YouTubeDataAPI', 'YouTube.Lib');

class YouTubeDataAPIComponent extends Component {
	
	public function getGrantAccessURL($clientID, $redirectURI, $accessType) {
		return YouTubeDataAPI::getGrantAccessUrl($clientID, $redirectURI, $accessType);
	}

}

?>