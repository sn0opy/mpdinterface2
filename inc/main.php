<?php

class main extends controller {
	public function start() {
		$f3 = $this->framework;
		$mpd = $this->mpd;
		
		$f3->set('playlist', $mpd->getPlaylist());
		$f3->set('streamType', $mpd->getStreamType($f3->get('mpd_httpd_host'), $f3->get('mpd_httpd_port')));
	}
	
	public function control() {
		$f3 = $this->framework;
		$mpd = $this->mpd;
		
		$option = $f3->get('PARAMS.option');
		$param = $f3->get('PARAMS.param');
		
		switch($option) {
			case 'pause':
				$mpd->controlPause();
				break;
			case 'play':
				$mpd->controlPlay();
				break;
			case 'playback':
				$mpd->controlPlayback((int)$param);
				break;
			case 'stop':
				$mpd->controlStop();
				break;
			case 'next':
				$mpd->controlNext();
				break;
			case 'previous':
				$mpd->controlPrevious();
				break;
		}
		exit();
	}
	
	public function status() {
		$f3 = $this->framework;
		$mpd = $this->mpd;
		
		echo json_encode(array(
			"status" => $mpd->getState(),
			"currentTrack" => $mpd->getCurrentTrackInfo()
		));
		exit;
	}
}
