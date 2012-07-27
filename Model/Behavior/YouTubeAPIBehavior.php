<?php

class YouTubeAPIBehavior extends ModelBehavior {
	
	private $api;

	public $_defaults = array();

	public function setup(Model $model, $settings = array()) {
		$this->_defaults = Set::merge($this->_defaults, $settings);
		$this->api = new Zend_Gdata_Youtube();
	}

	public function api() {
		return $this->api;
	}

	public function getVideoData($id) {
		return $this->api->getVideoFeed('https://gdata.youtube.com/feeds/api/videos/videoid?v=' . $id);
	}

}

?>