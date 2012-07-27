<?php

class YouTubeHelper extends AppHelper {
	
	public $helpers = array('Html', 'Form', 'Js');
	public $view;

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->view = $View;
	}

	public function embed($flashPlayerUrl, $width,  $height, $allowFullScreen = true) {
		$objectParams = array(
			'type' => 'application/x-shockwave-flash',
			'data' => $flashPlayerUrl,
			'width' => $width,
			'height' => $height
		);
		$params = array(
			array(
				'name' => 'movie',
				'value' => $flashPlayerUrl
			),
			array(
				'name' => 'allowFullScreen',
				'value' => ($allowFullScreen) ? 'true' : 'false'
			)
		);
		return $this->Html->tag(
			'object',
			$this->Html->tag(
				'param',
				'',
				$params[0]
			) . 
			$this->Html->tag(
				'param',
				'',
				$params[1]
			),
			$objectParams
		);
	}

}

?>