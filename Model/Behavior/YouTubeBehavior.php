<?php
App::uses('YouTubeDataAPI', 'YouTube.Lib');
App::uses('CakeSession', 'Cake/Model/Datasource');
App::uses('ClassRegistry', 'Cake/Utility');

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
		try {
			return $this->getVideoFeed($model, YouTubeDataAPI::USER_VIDEOS_URL, YouTubeDataAPI::CURRENT_LOGIN_USER, $accessToken);
		}
		catch(Zend_Gdata_App_HttpException $e) {
			$this->refreshToken($model, $e->getResponse());
			return $this->getVideoFeed($model, YouTubeDataAPI::USER_VIDEOS_URL, YouTubeDataAPI::CURRENT_LOGIN_USER, $accessToken);
		}
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

	public function getVideo(Model $model, $videoID, $accessToken) {
		return YouTubeDataAPI::getVideo($this->api, $videoID, $accessToken);
	}

	private function getVideoFeed(Model $model, $location, $user, $accessToken = null) {
		return YouTubeDataAPI::getUserVideos($this->api, $location, $user, $accessToken);
	}

	public function getSettings() {
		return $this->_defaults;
	}

	public function getAccessTokenField() {
		return self::ACCESS_TOKEN_FIELD;
	}

	public function getRefreshTokenField() {
		return self::REFRESH_TOKEN_FIELD;
	}

	public function refreshToken(Model $model, Zend_Http_Response $response) {
		$oldAccessToken = CakeSession::read('YouTube.Auth.access_token');
		$clientID = Configure::read('YouTube.client_id');
		$clientSecret = Configure::read('YouTube.client_secret');
		CakeSession::write('YouTube.Auth', YouTubeDataAPI::refreshToken(
			$response, 
			CakeSession::read('YouTube.Auth'),
			$clientID,
			$clientSecret
		));
		$accessToken = CakeSession::read('YouTube.Auth.access_token');
		if($oldAccessToken != $accessToken) {
			$userModel = ClassRegistry::init(Configure::read('YouTube.userClass'));
			$userModel->Behaviors->load('YouTube.YouTube');
			$userModel->id = CakeSession::read(Configure::read('YouTube.authComponent').'.'.Configure::read('YouTube.userModel').'.id');
			$userModel->saveToken($accessToken, CakeSession::read('YouTube.Auth.refresh_token'));
		}
	}

}

?>