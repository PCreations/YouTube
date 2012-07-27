<?php

class YouTubeController extends YouTubeAppController {
	
	public $components = array('Session');

	public function oauth2callback() {
		$this->autoRender = false;
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		$results = $HttpSocket->post('https://accounts.google.com/o/oauth2/token', array(
			'code' => $_GET['code'],
			'client_id' => Configure::read('YouTube.client_id'),
			'client_secret' => Configure::read('YouTube.client_secret'),
			'redirect_uri' => 'http://localhost/lsbox/you_tube/you_tube/oauth2callback',
			'grant_type' => 'authorization_code'
		), array('redirect' => true));
		$this->Session->write('YouTube.authJSON', $results->body());
		$this->redirect($this->Session->read('YouTube.authReferer'));
	}

}

?>