<?php

/**
 * Class videoFile
 */
class videoFile
{

    /**
     * @var string The series Name
     */
    private $seriesName;

    /**
     * @var string The season number
     */
    private $season;

    /**
     * @var string The episode number
     */
    private $episode;

    /**
     * @var string The file location
     */
    private $fileLocation;

    /**
     * @var string The fileName
     */
    private $fileName;

    /**
     * @var mixed The series slug
     */
    private $seriesSlug;

    /**
     * @var bool If the video is part of a series
     */
    private $isSeries = false;

    /**
     * @var string The video file extension
     */
    private $fileExtension;

    /**
     * @var int|null The video Quality
     */
    private $videoQuality;

    /**
     * @var string The fileName without exten
     */
    private $fileNameOnly;

    /**
     * videoFile constructor.
     * @param $filePath string The location of the video
     */
    public function __construct($filePath)
    {

        $pathInfo = pathinfo($filePath);
        $this->fileLocation = $pathInfo['dirname'] . '/';
        $this->fileNameOnly = $pathInfo['fileName'];
        $this->fileExtension = $pathInfo['extension'];
        $this->fileName = $this->fileNameOnly . '.' . $this->fileExtension;
        //replace all un scannible characters
        $fixedFileName = $this->fileName;

        $replace = array('[', ']', ')', '(');
        $fixedFileName = str_replace($replace, '', $fixedFileName);

        $replace = array('_', ' ', '-');
        $fixedFileName = str_replace($replace, '.', $fixedFileName);

        $pRegs = array(
            '/\.S([0-9]{1,2})E([0-9]{1,2})\./i' => '/(.*?)\.S[0-9]{1,2}E[0-9]{1,2}/i',
            '/\.([0-1]?[0-9]){1}([0-9]{2})\./' => '/(.*?)\.[0-9]{1}[0-9]{2}\./',
            '/\.([0-9]){1}x([0-9]{2})\./i' => '/(.*?)\.[0-9]{1}x[0-9]{2}\./i',
            '/\.Season\.([0-9]){1,2}\.([0-9]{1,2})/i' => '/(.*?)\.Season\.([0-9]){1,2}\.([0-9]{1,2})/i',
            '/\.S([0-9]{1,2})\.([0-9]{1,2})\./i' => '/(.*?)\.S[0-9]{1,2}\.[0-9]{1,2}/i',
            '/\.Season\.([0-9]){1,2}\.Episode\.([0-9]{1,2})/i' => '/(.*?)\.Season\.([0-9]){1,2}\.Episode\.([0-9]{1,2})/i',
            '/\.Season\.([0-9]){1,2}\.+([0-9]{1,2})/i' => '/(.*?)\.Season\.([0-9]){1,2}\.+([0-9]{1,2})/i');

        foreach ($pRegs as $search => $get) {
            $matches = array();
            if (1 === preg_match($search, $fixedFileName, $matches)) {
                $this->isSeries = true;
                $this->season = sprintf('%02d', $matches[1]);
                $this->episode = sprintf('%02d', $matches[2]);

                $seriesMatches = array();
                preg_match($get, $fixedFileName, $seriesMatches);
                $this->seriesSlug = $seriesMatches[1];
                $this->seriesName = ucwords(strtolower(trim(implode(' ', explode('.', $this->seriesSlug)))));

                $getID3 = new getID3;
                $ThisFileInfo = $getID3->analyze($filePath);
                $this->videoQuality = $ThisFileInfo['video']['resolution_y'];

                //its a movie if its longer than 70 minutes
                if ((int)$ThisFileInfo['playtime_seconds'] > 4200) {
                    $this->isSeries = false;
                }
                break;
            }
        }


    }

    /**
     * Get the video quality
     * @return int|null The video quality
     */
    public function getVideoQuality()
    {
        return $this->videoQuality;
    }

    /**
     * Get the fileName
     * @return string
     */
    public function getfileName()
    {
        return $this->fileName;
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
     * Get the series name
     * @return string The series name
     */
    public function getSeries()
    {
        return $this->seriesName;
    }

    /**
     * Get the episode
     * @return string The episode number
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * Get the season number
     * @return string The season number
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Get the series slug
     * @return mixed Get the series Slug
     */
    public function getSeriesSlug()
    {
        return $this->seriesSlug;
    }

    /**
     * Get the file extension
     * @return mixed The file extension
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Is the video part of a series
     * @return bool Is the video part of a series
     */
    public function isSeries()
    {
        return $this->isSeries;
    }

    /**
     * Get the fileName without the extension
     * @return mixed The fileName without extension
     */
    public function getfileNameOnly()
    {
        return $this->fileNameOnly;
    }
}
