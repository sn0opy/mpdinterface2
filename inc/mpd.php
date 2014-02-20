<?php

/**
 * PHP class to interact with MPD
 * 
 * This mpd class is based on http://mpd.24oz.com which is
 * not maintained anymore since 2004. This class aims to
 * use the current PHP standards and make it less bloated.
 * 
 * 
 * @author Sascha Ohms <sasch9r@gmail.com>
 * @copyright Copyright (c) 2014, Sascha Ohms
 * @version 0.0.1
 */


class Mpd {
	/**
	 * Host of MPD server
	 * 
	 * @var string 
	 */
	public $host;
	
	/**
	 * Port of MPD server
	 * 
	 * @var int
	 */
	public $port;	
    
	/**
	 * Password of MPD server
	 * 
	 * @var string
	 */
	public $password;	
    
	/**
	 * socket which will be used to communicate
	 * 
	 * @var mixed  
	 */
	public $sock;	
	
	/**
	 * indicates an established connection to the server
	 * 
	 * @var bool 
	 */
	public $connected;
	
	/*
	 * 
	 */
	public $playlist;
    
	/*
	 * 
	 */
	public $state;
	
	/**
	 * Set initial parameters and connect to server
	 * 
	 * @param string $host
	 * @param int $port
	 * @param string $password 
	 */
	public function __construct($host = 'localhost', $port = 6600, $password = null) {
		$this->host = $host;
		$this->port = $port;
		$this->password = $password;
		
		$this->connected = false;
		
		return $this->_connect();
	}
    
    /**
	 *
     */
    public function __destruct() {
        $this->_disconnect();
    }	
    
    /**
     *
     */
    private function _connect() {
		$this->sock = @fsockopen($this->host, $this->port, $errno, $errstr, 15);

		if(!$this->sock)
			return null;

		$resp = fgets($this->sock, 128);

		if(strpos($resp, 'OK') === 0) {
			$this->connected = true;
		
			if(!is_null($this->password))
				if($this->_sendCmd('password', $this->password))
					return true;
		}
		
		return null;
	}   
	
    /**
	 * 
     */
    private function _disconnect() {
        @fclose($this->sock);
    }

    /**
     *
     */
	private function _parseFileListResp($resp) {
		if(is_null($resp)) {
			return NULL;
		} else {
			$plistArray = array();
			$plistLine = strtok($resp, "\n");
			$plistFile = "";
			$plCounter = -1;

			while($plistLine) {
				list($element, $value) = explode(": ", $plistLine);
				if($element == "file") {
					$plCounter++;
					$plistFile = $value;
					$plistArray[$plCounter]["file"] = $plistFile;
				} else {
					$plistArray[$plCounter][$element] = $value;
				}

				$plistLine = strtok("\n");
			} 
		}
		return $plistArray;
	}
    
    /**
     * send commands to a mpd server
     *
     * @param type $arg 
     */
    private function _sendCmd($command, $arg1 = FALSE, $arg2 = FALSE) {
		if($arg1!==FALSE) $command .= ' '.$arg1;		
		if($arg2!==FALSE) $command .= ' '.$arg2;

		if($this->sock)
			@fwrite($this->sock, $command."\n");

		$resp = NULL;
		while(($line = @fgets($this->sock)) != stristr($line, 'OK'))
			$resp .= $line;
		
		return $resp;
    }

	/**
	 * 
	 */
    public function getPlaylist() {
		$this->playlist = $this->_parseFileListResp($this->_sendCmd('playlistinfo'));
		return $this->playlist;
    }
	
	/*
	 * 
	 */
	public function getStreamType($host = 'localhost', $port = '8000') {
		$headers = @get_headers('http://'.$host.':'.$port, 1);
		return $headers['Content-Type'] ? $headers['Content-Type'] : FALSE;
	}
	
	/*
	 * 
	 */
	public function getState() {
		$status = $this->getCurrentStatus() ? $this->getCurrentStatus() : 'stop';
		return $status;
	}
	
	/*
	 * 
	 */
	public function getCurrentStatus() {
		$rows = explode("\n", $this->_sendCmd('status'));
		$arr = array();
		foreach($rows as $row) {
			$ex = explode(': ', $row);
			if(isset($ex[1]) && isset($ex[0]))
				$arr[strtolower($ex[0])] = $ex[1];
		}	
		$this->playerStatus = $arr;
		return $this->playerStatus;
	}
	
	/*
	 * 
	 */
	public function controlPause() {
		$this->_sendCmd('pause');
	}
	
	/*
	 * 
	 */
	public function controlPlay() {
		$this->_sendCmd('play');
	}
	
	/*
	 * 
	 */
	public function controlNext() {
		$this->_sendCmd('next');
	}
	
	/*
	 * 
	 */
	public function controlPrevious() {
		$this->_sendCmd('previous');
	}
	
	/*
	 * 
	 */
	public function controlStop() {
		$this->_sendCmd('stop');
	}

	/*
	 *
	 */
	public function controlPlayback($songId) {
		if(is_int($songId)) {
			$this->_sendCmd('play', $songId);
		}
	}
	
	/*
	 * 
	 */
	public function getCurrentTrackInfo() {
		$l = explode("\n", $this->_sendCmd('currentsong'));		
		foreach($l as $r) {
			$ex = explode(': ', $r);
			if(isset($ex[1]) && isset($ex[0]))
				$arr[strtolower($ex[0])] = $ex[1];
		}
		return $arr;
	}
}
