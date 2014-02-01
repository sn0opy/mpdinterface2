<?php

class controller {
	protected $framework;
	protected $mpd;
	
	public function __construct() {
		$this->framework = Base::instance();
		$this->mpd = new Mpd($this->framework->get('mpd_host'), $this->framework->get('mpd_port'), $this->framework->get('mpd_password'));		
	}
	
	public function afterRoute() {
		echo Template::instance()->render('main.tpl.php');
	}
}
