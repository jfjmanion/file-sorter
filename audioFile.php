<?php

/**
 * Class audioFile
 */
class audioFile
{

    /**
     * @var string Title of the song
     */
    private $title;

    /**
     * @var string The songs Album
     */
    private $album;

    /**
     * @var int The Album year
     */
    private $albumYear;

    /**
     * @var string The track's artist
     */
    private $artist;

    /**
     * @var string The location of the file
     */
    private $fileLocation;

    /**
     * @var string The filename
     */
    private $filename;

    /**
     * @var string The file extension
     */
    private $fileExtension;

    /**
     * @var string The track number on the album
     */
    private $trackNumber = 0;

    /**
     * audioFile constructor.
     * @param $filePath string The location of the file
     */
    public function __construct($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $this->fileLocation = $pathInfo['dirname'] . '/';
        $this->filename = $pathInfo['filename'] . '.' . $pathInfo['extension'];
        $this->fileExtension = $pathInfo['extension'];

        $getID3 = new getID3;
        $thisFileInfo = $getID3->analyze($filePath);
        getid3_lib::CopyTagsToComments($thisFileInfo);
        
        if (array_key_exists('track_number', $thisFileInfo['comments'])) {
        	$trackNumber = $thisFileInfo['comments']['track_number'][0];
        	$this->trackNumber = !empty($trackNumber) ? $trackNumber : $thisFileInfo['comments']['track'][0];
        	$this->trackNumber = sprintf('%02d', $this->trackNumber);
    	}

        $this->artist = ucwords(strtolower($thisFileInfo['comments']['artist'][0]));
        $this->albumYear = $thisFileInfo['comments']['year'][0];
        $this->album = ucwords(strtolower($thisFileInfo['comments']['album'][0]));
        $this->title = ucwords(strtolower($thisFileInfo['comments']['title'][0]));

        unset($thisFileInfo);
    }

    /**
     * Get the artist
     * @return string The artist
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Get the album year
     * @return int The album year
     */
    public function getAlbumYear()
    {
        return $this->albumYear;
    }

    /**
     * Get the album name
     * @return string The album name
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Get the file extension
     * @return string the track's file extension
     */
    public function getExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Get the file location
     * @return string The file location
     */
    public function getFileLocation()
    {
        return $this->fileLocation;
    }

    /**
     * Get the file name
     * @return string The file name
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * Get the song title
     * @return string The song title 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the track number
     * @return string The track number
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }
}
