<?php

class YouTubeHelper extends AppHelper {
	
	public $helpers = array('Html', 'Form', 'Js');
	public $view;
	public $settings = array();

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->view = $View;
		$this->settings = Hash::merge($this->settings, $settings);
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

	public function blocInfos($video, $element = 'YouTube.youtube_bloc_infos') {
		echo $this->view->element($element, array(
			'title' => $video->getVideoTitle(),
			'description' => $video->getVideoDescription(),
			'thumbnails' => $video->getVideoThumbnails(),
			'duration' => $video->getVideoDuration(),
			'id' => $video->getVideoId(),
		));
	}

	public function createHiddenFields($video) {
		echo $this->Form->hidden('yt_id', array('id' => 'yt_id'));
	}

}

?>