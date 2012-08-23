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
		$accessTokenField = $this->controller->{$this->modelClass}->getAccessTokenField();
		$refreshTokenField = $this->controller->{$this->modelClass}->getRefreshTokenField();

		/* Check for stored authenticate data */
		$userModel = Configure::read('YouTube.userModel');
		$test = $this->controller->loadModel('YouTube.YouTubeAccess');
		$authData = $this->controller->YouTubeAccess->find('first', array(
			'conditions' => array(
				'YouTubeAccess.user_id' => $this->Session->read(Configure::read('YouTube.authComponent') . '.User.id')
			),
			'contain' => array()
		));
		if($authData !== false && $authData['YouTubeAccess']['yt_access_token'] != '' && $authData['YouTubeAccess']['yt_refresh_token'] != '') {
			$this->Session->write('YouTube.Auth.access_token', $authData['YouTubeAccess'][$accessTokenField]);
			$this->Session->write('YouTube.Auth.refresh_token', $authData['YouTubeAccess'][$refreshTokenField]);
		}
		if(!$this->Session->check('YouTube.Auth') && !$this->Session->check('YouTube.Auth.access_denied')) {
			$this->Session->write('YouTube.authReferer', FULL_BASE_URL . Router::url());
			$this->controller->redirect(
				$this->YouTubeDataAPI->getGrantAccessURL(
					Configure::read('YouTube.client_id'), 
					$this->OAUTH2_CALLBACK, 
					$accessType
				)
			);
		}
		return $this->Session->check('YouTube.Auth');
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

	public function setPrefillData($videoID) {
		$accessToken = $this->getAccessToken();
		try {
			$video = $this->controller->{$this->modelClass}->getVideo($videoID, $accessToken);
		}
		catch(Zend_Gdata_App_HttpException $e) {
			$this->controller->{$this->modelClass}->refreshToken($e->getResponse());
			$video = $this->controller->{$this->modelClass}->getVideo($videoID, $accessToken);
		}
		$videoFields = $this->controller->{$this->modelClass}->getSettings();
		$this->controller->request->data[$this->modelClass][$videoFields['title']] = $video->getVideoTitle();
		$this->controller->request->data[$this->modelClass][$videoFields['description']] = $video->getVideoDescription();
		$this->controller->request->data[$this->modelClass][$videoFields['duration']] = $video->getVideoDuration();
		unset($this->controller->request->data[$this->modelClass]['completeForm']);
		$this->controller->set(array('yt_flashPlayerUrl' => $video->getFlashPlayerUrl()));
	}

	public function getAccessToken() {
		return $this->Session->check('YouTube.Auth.access_token') ? $this->Session->read('YouTube.Auth.access_token') : null;
	}

}
?>