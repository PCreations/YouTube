<?php
App::uses('HttpSocket', 'Network/Http');

class YouTubeComponent extends Component {
	
	public $components = array('Session', 'YouTube.YouTubeDataAPI');
	public $controller;
	public $modelClass;

	private $OAUTH2_CALLBACK = array(
		'controller' => 'you_tube',
		'action' => 'oauth2callback',
		'plugin' => 'you_tube',
		'full_base' => true
	);

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->modelClass = $controller->modelClass;
		$this->OAUTH2_CALLBACK = Router::url($this->OAUTH2_CALLBACK);
	}

	public function test() {
		echo "test";
		//$yt = new Zend_Gdata_YouTube();
		//$videoFeed = $yt->getVideoFeed('http://gdata.youtube.com/feeds/users/grafikrt/uploads');
	}

/**
 * Called to authenticate user with YouTube via Oauth2.0 protocol
 */
	public function auth($accessType = 'offline') {
		if(!$this->Session->check('YouTube.Auth')) {
			$this->Session->write('YouTube.authReferer', FULL_BASE_URL . Router::url());
			$this->controller->redirect(
				$this->YouTubeDataAPI->getGrantAccessURL(
					Configure::read('YouTube.client_id'), 
					$this->OAUTH2_CALLBACK, 
					$accessType
				)
			);
		}
		debug($this->Session->read('YouTube.Auth'));
	}

	

	public function authSub() {
		/* Check if user already authenticate */
		if(!$this->Session->check('YouTube.token')) {
			$next = Router::url(
				array(
					'controller' => 'you_tube',
					'action' => 'authSub',
					'plugin' => 'you_tube',
					'full_base' => true
				)
			);
			
			$this->Session->write('YouTube.authSubReferer', FULL_BASE_URL . Router::url());
			$scope = "http://gdata.youtube.com";
			$secure = false;
			$session = true;
			$this->controller->redirect(Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, $session));
		}
		else {
			debug($this->Session->read('YouTube.token'));
			debug($this->Session->read('YouTube.authSubReferer'));
			return new Zend_Gdata_YouTube(Zend_Gdata_AuthSub::getHttpClient($this->Session->read('YouTube.token')));
		}
	}

}
?>