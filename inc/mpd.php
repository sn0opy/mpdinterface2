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
 * @copyright Copyright (c) 2013, Sascha Ohms
 * @version 0.0.1
 */

define('MPD_CMD_STATUS',      'status');
define('MPD_CMD_STATISTICS',  'stats');
define('MPD_CMD_VOLUME',      'volume');
define('MPD_CMD_SETVOL',      'setvol');
define('MPD_CMD_PLAY',        'play');
define('MPD_CMD_STOP',        'stop');
define('MPD_CMD_PAUSE',       'pause');
define('MPD_CMD_NEXT',        'next');
define('MPD_CMD_PREV',        'previous');
define('MPD_CMD_PLLIST',      'playlistinfo');
define('MPD_CMD_PLADD',       'add');
define('MPD_CMD_PLREMOVE',    'delete');
define('MPD_CMD_PLCLEAR',     'clear');
define('MPD_CMD_PLSHUFFLE',   'shuffle');
define('MPD_CMD_PLLOAD',      'load');
define('MPD_CMD_PLSAVE',      'save');
define('MPD_CMD_KILL',        'kill');
define('MPD_CMD_REFRESH',     'update');
define('MPD_CMD_REPEAT',      'repeat');
define('MPD_CMD_LSDIR',       'lsinfo');
define('MPD_CMD_SEARCH',      'search');
define('MPD_CMD_START_BULK',  'command_list_begin');
define('MPD_CMD_END_BULK',    'command_list_end');
define('MPD_CMD_FIND',        'find');
define('MPD_CMD_RANDOM',      'random');
define('MPD_CMD_SEEK',        'seek');
define('MPD_CMD_PLSWAPTRACK', 'swap');
define('MPD_CMD_PLMOVETRACK', 'move');
define('MPD_CMD_PASSWORD',    'password');
define('MPD_CMD_TABLE',       'list');

define('MPD_RESPONSE_ERR',	  'ACK');
define('MPD_RESPONSE_OK',     'OK');

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
     * close socket to server
     */
    public function __destruct() {
        $this->_disconnect();
    }	
    
    /**
     * connects to a mpd server
     * 
     * @return mixed
     */
    private function _connect() {
		$this->sock = @fsockopen($this->host, $this->port, $errno, $errstr, 15);

		if(!$this->sock)
			return null;

		$resp = fgets($this->sock, 128);

		if(strpos($resp, MPD_RESPONSE_OK) === 0) {
			$this->connected = true;
		
			if(!is_null($this->password))
				if($this->_sendCmd(MPD_CMD_PASSWORD, $this->password))
					return true;
		}
		
		return null;
	}   
	
    /**
	 * 
     * disconnects from the mpd server
     */
    private function _disconnect() {
        @fclose($this->sock);
    }

    /**
     *
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
    private function _sendCmd($command, $arg1 = false, $arg2 = false) {
		if($arg1) $command .= ' '.$arg1;		
		if($arg2) $command .= ' '.$arg2;

		if($this->sock)
			@fwrite($this->sock, $command."\n");

		$resp = NULL;
		while(($line = @fgets($this->sock)) != stristr($line, MPD_RESPONSE_OK))
			$resp .= $line;
		
		return $resp;
    }

	/**
	 * 
	 */
    public function getPlaylist() {
		$this->playlist = $this->_parseFileListResp($this->_sendCmd(MPD_CMD_PLLIST));
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
	public function getState($status = false) {
		$status = $status ? $status : $this->getCurrentStatus();
		return $this->playerStatus['state'];
	}
	
	/*
	 * 
	 */
	public function getCurrentStatus() {
		$rows = explode("\n", $this->_sendCmd(MPD_CMD_STATUS));
		foreach($rows as $row) {
			$ex = explode(': ', $row);
			if(isset($ex[1]) && isset($ex[0]))
				$arr[$ex[0]] = $ex[1];
		}	
		$this->playerStatus = $arr;
		return $this->playerStatus;
	}
	
	/*
	 * 
	 */
	public function controlPause() {
		$this->_sendCmd(MPD_CMD_PAUSE);
	}
	
	/*
	 * 
	 */
	public function controlPlay() {
		$this->_sendCmd(MPD_CMD_PLAY);
	}
	
	/*
	 * 
	 */
	public function controlNext() {
		$this->_sendCmd(MPD_CMD_NEXT);
	}
	
	/*
	 * 
	 */
	public function controlPrevious() {
		$this->_sendCmd(MPD_CMD_PREV);
	}
	
	/*
	 * 
	 */
	public function controlStop() {
		$this->_sendCmd(MPD_CMD_STOP);
	}

	/*
	 *
	 */
	public function controlPlayback($songId) {
		if(is_int($songId)) {
			$this->_sendCmd(MPD_CMD_PLAY, $songId);
		}
	}
	
	/*
	 * 
	 */
	public function getCurrentTrackInfo() {
		$playlist = ($this->playlist != NULL) ? $this->playlist : $this->getPlaylist();
		$status = ($this->playerStatus != NULL) ? $this->playerStatus : $this->getCurrentStatus();

		if(!isset($status['songid'])) {
			return false;
		} else {
			$id = $status['songid'];
			foreach($playlist as $key => $song) {
				if($song['Id'] == $id) {
					return array(
						'id' => $playlist[$key]['Id'],
						'artist' => isset($playlist[$key]['Artist']) ? $playlist[$key]['Artist'] : '',
						'title' => isset($playlist[$key]['Title']) ? $playlist[$key]['Title'] : '',
						'album' => isset($playlist[$key]['Album']) ? $playlist[$key]['Album'] : '',
						'time' => isset($playlist[$key]['Time']) ? $playlist[$key]['Time'] : '', 
						'track' => isset($playlist[$key]['Track']) ? $playlist[$key]['Track'] : '',
						'genre' => isset($playlist[$key]['Genre']) ? $playlist[$key]['Genre'] : ''
					);
				}
			}
		}
	}
}
