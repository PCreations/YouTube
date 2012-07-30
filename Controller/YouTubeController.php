<?php

class YouTubeController extends YouTubeAppController {
	
	public $components = array('Session', 'YouTube.YouTubeDataAPI');
	private $authData;

	const REDIRECT_URI = 'http://localhost/lsbox/you_tube/you_tube/oauth2callback';

	public function oauth2callback() {
		$this->autoRender = false;
		//User refused connection
		if(isset($_GET['error'])) {
			$this->Session->write('YouTube.Auth.access_denied', 'true');
			$this->redirect($this->Session->read('YouTube.authReferer'));
		}
		$authData = $this->YouTubeDataAPI->getAccessToken(
			$_GET['code'],
			Configure::read('YouTube.client_id'),
			Configure::read('YouTube.client_secret'),
			self::REDIRECT_URI
		);
		$authData = json_decode($authData);
		try {
			$this->_saveToken($authData);
		} catch(CakeException $e) {
			$e->getMessage();
		}
		die("after save in controller");
		$this->Session->write('YouTube.Auth.access_token', $authData->access_token);
		$this->Session->write('YouTube.Auth.refresh_token', $authData->refresh_token);
		$this->redirect($this->Session->read('YouTube.authReferer'));
	}

	private function _saveToken($authData) {
		debug("dans _saveToken");
		$userClass = Configure::read('YouTube.userClass');
		$userModel = Configure::read('YouTube.userModel');
		$authComponent = Configure::read('YouTube.authComponent');
		$userID = $this->Session->read($authComponent.'.'.$userModel.'.id');
		try {
			$this->loadModel($userClass, $userID);
		} catch(MissingModelException $e) {
			$e->getMessage();
		}
		debug($this->{$userModel});
		$this->{$userModel}->id = $userID;
		debug($this->{$userModel}->Behaviors->load('YouTube.YouTube'));
		debug('After load behavior');
		$this->{$userModel}->saveToken($authData->access_token, $authData->refresh_token);
	}

}

?>