<?php
App::uses('YouTubeDataAPI', 'YouTube.Lib');

class YouTubeDataAPIBehavior extends ModelBehavior {
	
	private $api;

	public function __construct() {
		$this->api = new Zend_Gdata_Youtube();
	}

	

}

?>