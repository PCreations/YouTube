<?php

class YouTubeAPIBehavior extends ModelBehavior {
	
	private $api;
	const ACCESS_TOKEN_FIELD = 'yt_access_token';
	const REFRESH_TOKEN_FIELD = 'yt_refresh_token';

	public $_defaults = array();

	public function setup(Model $model, $settings = array()) {
		$this->_defaults = Set::merge($this->_defaults, $settings);
		$this->api = new Zend_Gdata_Youtube();
	}

	public function api() {
		return $this->api;
	}

	public function getVideoData(Model $model, $id) {
		return $this->api->getVideoFeed('https://gdata.youtube.com/feeds/api/videos/videoid?v=' . $id);
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

}

?>