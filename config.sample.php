<?php

class Config {
	
	public $seriesDir = './series/';

	public $moviesDir = './movies/';

	public $musicDir = './music/';

	public $manualSort = './manual_sort/';

	public $scanningDir;

	public $videoMatch = array('wmv', 'mkv', 'avi', 'mp4','mpg','m4v','rmvb');

	public $audioMatch = array('m4a', 'mp3', 'flac', 'wma');

	public $permissions = 0777;

	public $debug = true;

	public $prod = true;

	public $lockFile;

	public $ignoreFiles = array('txt', 'nfo', 'jpg', 'DS_Store', 'm3u', 'log');

	public function __construct() {
		$this->scanningDir = dirname(__FILE__) . '/files_to_move';
		$this->lockFile = $this->scanningDir . "lock";	
	}
}
