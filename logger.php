<?php
///////////////  Includes
include "./config/configurer.php";

//////////////  Constants 
define("identGuide", "    ");
define('separator', "  ");

//////////////  Exceptions
class InvalidFailuresNumber extends Exception
{
    private $errno = 'Invalid failures number received!';

    public function __construct()
    {
        echo $this->errno;
        die();
    }
}

class InvalidAction extends Exception
{
    private $errno = 'Invalid Action found!';
}


/////////////   Classes

class LoadLogFile
{
    public $source = "";
    public $logs   = [];
    protected $gotData = false;
    private $objConfig;

    private function getConfig()
    {
        $this->objConfig = new Configurations("./config/configurations.json");
    }

    public function __construct($sourceFile)
    {
        $this->source = $sourceFile;
        $this->logs = json_decode(file_get_contents($sourceFile));
        $this->gotData = true;
        $this->getConfig();
    }

    private function updateLogFile()
    {
        if (!$this->gotData) {
            throw new InvalidAction();
        }
        $rawData = json_encode($this->logs);
        file_put_contents($this->source, $rawData);
    }

    public function listLogs($onWeb = false)
    {
        if (!$this->gotData) {
            throw new InvalidAction();
        }
        $rtData = "";
        if ($onWeb) {
            $rtData = $rtData . "<table>Logs From \n";
            for ($i = 0; $i < count($this->logs); $i++) {
                // set the headers
                $rtData = $rtData . "<th>\n";
                foreach ($this->logs[$i] as $key => $value) {
                    $rtData = $rtData . "<td>" . $key . "</td>\n";
                }
                $rtData = $rtData . "</th>\n";
                $rtData = $rtData . "<tr>\n";
                foreach ($this->logs[$i] as $header => $value) {
                    $rtData = $rtData . "<td>" . $value . "</td>\n";
                }
                $rtData = $rtData . "</tr>\n";
            }
            $rtData = $rtData . "</table>\n";
        } else {
            for ($i = 0; $i < count($this->logs); $i++) {
                $rtData = $rtData . "Log Number " . $i . "\n";
                foreach ($this->logs[$i] as $key => $value) {
                    $rtData = $rtData . identGuide . "$key => $value\n";
                }
            }
        }
        return $rtData;
    }

    public function addLog($action, $failures = 0, $failureCode = 0)
    {
        if (!$this->gotData) {
            throw new InvalidAction();
        }
        $date = date($this->objConfig->document->DateConf);
        $hour = date($this->objConfig->document->HourConf);
        array_push($this->logs, array(
            'Date' => $date,
            'Time' => $hour,
            'Action' => $action,
            'Failures' => $failures,
            'ErrorCode' => $failureCode
        ));
        $this->updateLogFile();
    }

    public function clearLogs()
    {
        if (!$this->gotData) {
            throw new InvalidAction();
        }
        $this->logs = [];
        $this->updateLogFile();
    }


    public function getLogsNumber()
    {
        if (!$this->gotData) {
            throw new InvalidAction();
        }
        return count($this->logs);
    }

    public function queryByDate($queryDate)
    {
        $results = [];
        for ($i = 0; $i < $this->getLogsNumber(); $i++) {
            if ($this->logs[$i]->Date == $queryDate) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByError($failed = true)
    {
        $results = [];
        for ($i = 0; $i < $this->getLogsNumber(); $i++) {
            if ($failed) {
                if ($this->logs[$i]->Failures != "0") {
                    array_push($results, $this->logs[$i]);
                } else { }
            } else {
                if ($this->logs[$i]->Failures == "0") {
                    array_push($results, $this->logs[$i]);
                } else { }
            }
        }
        return $results;
    }

    public function queryByCode($errorCode)
    {
        $results = [];
        for ($i = 0; $i < $this->getLogsNumber(); $i++) {
            if ($this->logs[$i]->ErrorCode == $errorCode) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByTime($timeOf, $by = 0)
    {
        /**
         * @param by : 0 => Hours
         *             1 => Minutes
         *             2 => seconds
         * @param timeOf: The time value to query
         * @return array.
         */
        $results = [];
        for ($i = 0; $i < count($this->logs); $i++) {
            $splitedHour = explode(":", $this->logs[$i]->Time);
            if ($splitedHour[$by] == $timeOf) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByAction($action)
    {
        /**
         * 
         */
        $results = [];
        for ($i = 0; $i < count($this->logs); $i++) {
            if ($this->logs[$i]->Action == $action) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }
}

class LoadLogsTextFile
{
    private $document = [];
    private $sourceFile = "";
    private $gotData = false;
    private $confData;

    public function __construct($source)
    {
        $this->sourceFile = $source;
        $this->document = file_get_contents($this->sourceFile);
        $this->gotData = true;
        $this->confData = new Configurations("./config/");
    }

    public function listLogs()
    {
        $array = explode("\n", $this->document);
        echo count($array);
    }

    public static function splitsNewLineArray($data)
    {
        return explode("\n", $data);
    }

    public function addLog($action, $failureCode = 0, $failures = 0)
    {
        $date = date($this->confData->document->DateConf);
        $hour = date($this->confData->document->HourConf);
        $rtString = $date . separator . $hour . separator . $action . separator . $failures . separator . $failureCode . "\n";
        $this->document .= $rtString;
    }

    private function updateLogsFile()
    {
        file_put_contents($this->sourceFile, $this->document);
    }

    public function clearLogs(){ file_put_contents($this->sourceFile, ""); }

    public function queryByDate($dateTo)
    {
        $results = [];
        for ($i = 0; $i < count($this->logs); $i++) {
            $splitedData = explode(separator, $this->logs[$i]);
            if ($splitedData[0] == $dateTo) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByTime($timeTo, $by = 0)
    {
        /**
         * @param int by: 
         */
        $results = [];
        for ($i = 0; $i < count($this->logs); $i++) {
            $splitedData = explode(separator, $this->logs[$i]);
            $splitedHour = explode(":", $splitedData[1]);
            if ($splitedHour[$by] == $timeTo) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByFailures($failed = true){
        $results = [];
        $vlQuery = $failed? 0: 1;
        for ($i = 0; $i < count($this->logs); $i++) {
            $splitedData = explode(separator, $this->logs[$i]);
            if($splitedData[3] == $vlQuery || $splitedData[3] == "$vlQuery"){
                array_push($results, $this->logs[$i]);
            }
            else{}
        }
        return $results;
    }

    public function queryByErrorCode($errorCode = 0){
        $results = [];
        for ($i=0; $i < count($this->logs); $i++) { 
            $sploteData = explode(separator, $this->logs[$i]);
            if($sploteData[4] == $errorCode || $sploteData[4] == "$errorCode"){
                array_push($results, $this->logs[$i]);
            }
            else{}
        }
        return $results;
    }

    public function queryByAction($action){
        $results = [];
        for($i = 0; $i < count($this->logs); $i++){
            $splitedData = explode(separator, $this->logs[$i]);
            if($splitedData[2] == $action){ array_push($results, $this->logs[$i]); }
            else{}
        }
        return $results;
    }
}

?>