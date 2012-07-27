<?php
App::uses('YouTubeDataAPI', 'YouTube.Lib');

class YouTubeBehavior extends ModelBehavior {
	
	const ACCESS_TOKEN_FIELD = 'yt_access_token';
	const REFRESH_TOKEN_FIELD = 'yt_refresh_token';
	private $api;

	public $_defaults = array();

	public function setup(Model $model, $settings = array()) {
		$this->_defaults = Set::merge($this->_defaults, $settings);
		$this->api = new Zend_Gdata_Youtube();
	}

	public function getAuthUserVideos(Model $model, $accessToken) {
		return $this->getVideoFeed(YouTubeDataAPI::USER_VIDEO_URL, YouTubeDataAPI::CURRENT_LOGIN_USER, $accessToken);
	}

	public function saveToken(Model $model, $accessToken, $refreshToken) {
		$model->data[$model->alias][self::ACCESS_TOKEN_FIELD] = $accessToken;
		$model->data[$model->alias][self::REFRESH_TOKEN_FIELD] = $refreshToken;
		debug($model->id);
		debug($model->data);
		if(!$model->save($model->data[$model->alias], false)) {
			debug($model->validationErrors());
			echo "Erreur save";
		}
	}

	private function getVideoFeed($location, $user, $accessToken = null) {
		return YouTubeDataAPI::getUserVideos($this->api, $location, $user, $accessToken);
	}

}

?>