<?php
class MainProgram
{
	public function run($config) {

		$lineSeperator = "<br>";
		if (php_sapi_name() == 'cli') {
		  $lineSeperator = "\n\r";
		}

		echo "Script ran at: " . date('Y-m-d H:i:s') . $lineSeperator;
		if (file_exists($config->lockFile)) {
		 exit('Lock File Exists');
		}

		$videos = array();
		$audios = array();
		$others = array();
		$directories = array();
		$lockFlag = false;

		//get all of the files in the Completed folder and generate classes

		$recurseDir = new RecursiveDirectoryIterator($config->scanningDir);
		$objects = new RecursiveIteratorIterator($recurseDir, RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object) {
			$pathinfo = pathinfo($name);

			$extension = '';
			if (isset($pathinfo['extension'])) {
				$extension = strtolower($pathinfo['extension']);
			}

			if (in_array($extension, $config->videoMatch) && strpos($pathinfo['filename'], 'sample') === false ){
			  $videos[] = new videoFile($name);
			} else if (in_array($extension, $config->audioMatch)) {
			  $audios[] = new audioFile($name);
			} else if (!is_dir($name)){
			  $others[] = $name;
			} else {
				//don't delete the scanning dir
				$pathinfo = pathinfo($name);				
				if ($pathinfo['basename'] !== ".." && $pathinfo['basename'] !== ".") {
					$directories[] = $name;
				}
			}
		}
		foreach ($videos as $video) {
		
		  //make the directory
		  if ($video->isSeries()) {
			$directory = $config->seriesDir . $video->getSeries() . '/Season ' . $video->getSeason() . '/';

			$videoLocation = $directory . $video->getSeries() ." - s" .$video->getSeason() ."e". $video->getEpisode() . " (" .$video->getVideoQuality() . "p).".$video->getFileExtension();

			if ($config->debug) {
			  echo "Series: ". $video->getSeries() . $lineSeperator;
			  echo "Season: ".$video->getSeason() . $lineSeperator;
			  echo "Episode: ". $video->getEpisode() . $lineSeperator;
			  echo "Series Slug: ".$video->getSeriesSlug() . $lineSeperator;
			  echo "Old File Location: ".$video->getFileLocation() . $video->getFilename() . $lineSeperator;
			  echo "New File Location: ".$videoLocation . $lineSeperator . $lineSeperator;
			}
		  } else {
			//move it into the movies
			$directory = $config->moviesDir . $video->getFileNameOnly() . "/";
			$videoLocation = $directory . $video->getFilename();
			if ($config->debug){
			echo "new directory: " . $directory . $lineSeperator;
			echo "new video location: " . $videoLocation .$lineSeperator . $lineSeperator;
			}
		  }

		  if ($config->prod && !file_exists($config->lockFile)){
			//if it already exists, just delete it
			if (!file_exists($videoLocation)) {
			  @mkdir($directory, $config->permissions, true);
			    rename($video->getFileLocation().$video->getFilename(), $videoLocation);
				chmod($videoLocation, $config->permissions);
			} else {
			    unlink($video->getFileLocation().$video->getFilename());
			}
		  } else{
			$lockFlag = true;
		  }
		}

		foreach ($audios as $audio) {

		  $artist = $audio->getArtist();
		  $artist = (!empty($artist)) ? $artist . "/" : "various-artist/";
		  $year = $audio->getAlbumYear();
		  $year = (!empty($year)) ? " (" . $year . ")" : "";
		  $album = $audio->getAlbum();
		  $album = (!empty($album)) ? $album . $year . "/" : "default/";
		  $trackNumber = $audio->gettrackNumber();
		  $trackNumber = (!empty($trackNumber)) ? $trackNumber . " - " : "";

		  $directory = $config->musicDir . $artist . $album;

		  //make sure a title exists, otherwise will have no idea what it is
		  if ($audio->getTitle()  !== "") {
			$filename = $trackNumber . $audio->getTitle() . "." . $audio->getExtension();
		  } else {
			$filename = $audio->getFileName();
		  }
		  $newLocation = $directory . $filename;

		  if ($config->prod && !file_exists($config->lockFile)) {
			if (!file_exists($newLocation)) {
			  @mkdir($directory, $config->permissions, true);
			    rename($audio->getFileLocation().$audio->getFileName(), $newLocation);
				chmod($newLocation, $config->permissions);
			} else {
			    unlink($audio->getFileLocation() . $audio->getFileName());
			}
		  } else {
			$lockFlag = true;
		  }

		  if ($config->debug) {
		  echo "Old file location: " . $audio->getFileLocation(). $audio->getFileName() . $lineSeperator;
		  echo "New file location: " . $newLocation . $lineSeperator . $lineSeperator;
		  }
		}

		$archiveFiles = [];
		foreach ($others as $other) {
		  $copyFile = false;	
			
		  //move them into an new "Other to sort" folder
		  $pathinfo = pathinfo($other);
			if ($pathinfo['extension'] === 'rar') {
				$rarFile = rar_open($other);
				$list = rar_list($rarFile);
				foreach($list as $file) {
					$entry = rar_entry_get($rarFile, $file->getName());
					$entry->extract($config->scanningDir); // extract to the current dir
					$archiveFiles[$pathinfo['filename']]['extracted'] = true;
					$archiveFiles[$pathinfo['filename']]['files'][] = $other; 
					if ($config->debug){
						echo "UnRared File: " . $other . $lineSeperator;
					}
				}
				rar_close($rarFile);
				//if the rar file still exists after extraction, remove it later - multi part rar files fall into this
				if (file_exists($other)){
					$archiveFiles[$pathinfo['filename']]['extracted'] = true;	
					$archiveFiles[$pathinfo['filename']]['files'][] = $other;
				}
			} else if ( 1 === preg_match ('/r[0-9]{2}/', $pathinfo['extension'])){
			//get the filename and save it in an array. Once we unzip, we can delete it
				$archiveFiles[$pathinfo['filename']]['files'][] = $other; 
			} else {
				$copyFile = true;
			}
			
			
			
			//if the file is not part of an archive, send it to the manual sort
			if ($copyFile){
				if ($config->debug){
					echo "Copy Other File: " . $other . $lineSeperator;
					echo "To : " . $config->manualSort . $pathinfo['filename'] . "." . $pathinfo['extension'] . $lineSeperator . $lineSeperator;
				}

				if ($config->prod && !file_exists($config->lockFile)) {
					//if its a sample or the extension is ignore, just remove it
					if (!in_array($pathinfo['extension'], $config->ignoreFiles) && strpos($pathinfo['filename'], 'sample') === false) {
						rename($other, $config->manualSort . $pathinfo['filename'] . "." . $pathinfo['extension']);
					} else {
						unlink($other);
					}
				} else {
					$lockFlag = true;
				}
			}
		}
		//====Cleanup====//
		//Cleanup archive files if they've been extracted
		foreach ($archiveFiles as $archive) {
			if ($archive['extracted'] === true) {
				$archive['files'] = array_unique($archive['files']);
				if (is_array($archive['files'])) {
					foreach ($archive['files'] as $file){
						if ($config->debug) {
							echo "Delete archive File: " . $file . $lineSeperator;
						}
						if (file_exists($file)) {
							unlink($file);
						}
					}
				}
			}
		}
		
		
		//cleanup directories
		$directories = array_reverse($directories);
		foreach($directories as $directory){
		  if ($config->debug) {
			echo "Directory to delete: " . $directory . $lineSeperator . $lineSeperator;
		  }

		  if ($config->prod && $lockFlag === false){
		    @rmdir($directory);
		  }

		}
	}
}
