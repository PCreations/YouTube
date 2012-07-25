<?php

class YouTubeComponent extends Component {
	
	public function test() {
		echo "test";
		Zend_Loader::loadClass('Zend_Gdata_YouTube');
		$yt = new Zend_Gdata_YouTube();
		$videoFeed = $yt->getVideoFeed('http://gdata.youtube.com/feeds/users/grafikrt/uploads');
	}

}
?>