<?php

App::uses('ClassRegistry', 'Cake/Utility');

class YouTubeUserBehavior extends ModelBehavior {

	public $_defaults = array();

	public function setup(Model $model, $settings = array()) {
		$this->_defaults = Hash::merge($this->_defaults, $settings);
		$model->bindModel(
			array(
				'hasOne' => array(
					'YouTubeAccess' => array(
						'className' => 'YouTube.YouTubeAccess',
					)
				)
			),
			false
		);
	}
}


