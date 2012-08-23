<?php
App::uses('YouTubeAppModel', 'YouTube.Model');
App::uses('YouTubeBehavior', 'YouTube.Model/Behavior');
/**
 * YouTubeAccess Model
 *
 * @property User $User
 */
class YouTubeAccess extends YouTubeAppModel {

	public $actsAs = array('Containable');

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'yt_access';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function saveToken($userID, $accessToken, $refreshToken) {
		$this->create();
		$this->data['YouTubeAccess']['user_id'] = $userID;
		$this->data['YouTubeAccess'][YouTubeBehavior::ACCESS_TOKEN_FIELD] = $accessToken;
		$this->data['YouTubeAccess'][YouTubeBehavior::REFRESH_TOKEN_FIELD] = $refreshToken;
		if(!$this->save($this->data, false)) {
			echo "Erreur save";
		}
	}
}
