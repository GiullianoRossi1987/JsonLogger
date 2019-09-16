<?php
///////////////  Includes
require_once "./config/configurer.php";
require_once "./Exceptions.php";

//////////////  Namespaces Import
use LoggerErrors\InvalidFailureNumber;
use LoggerErrors\UnloadData;

//////////////  Constants 
define("identGuide", "    ");
define('separator', "  ");

/////////////   Classes

class LoadLogFile
{
    public $source = "";
    public $logs   = [];
    protected $gotData = false;
    private $objConfig;

    private function getConfig(){
        $this->objConfig = new Configurations("./config/configurations.json");
    }

    public function __construct(string $sourceFile){
        $this->source = $sourceFile;
        $this->logs = json_decode(file_get_contents($sourceFile));
        $this->gotData = true;
        $this->getConfig();
    }

    private function updateLogFile(){
        if (!$this->gotData) {
            throw new UnloadData();
        }
        $rawData = json_encode($this->logs);
        file_put_contents($this->source, $rawData);
    }

    public function listLogs(bool $onWeb = false)
    {
        if (!$this->gotData) {
            throw new UnloadData();
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

    public function addLog(string $action, int $failures = 0, int $failureCode = 0, bool $autoCommit = false)
    {
        /**
         * 
         */
        if (!$this->gotData) { throw new UnloadData(); }
        if($failures < 0){ throw new InvalidFailureNumber();}
        $date = date($this->objConfig->document->DateConf);
        $hour = date($this->objConfig->document->HourConf);
        array_push($this->logs, array(
            'Date' => $date,
            'Time' => $hour,
            'Action' => $action,
            'Failures' => $failures,
            'ErrorCode' => $failureCode
        ));
        if($autoCommit){ $this->updateLogFile();}
    }

    public function clearLogs()
    {
        if (!$this->gotData) {
            throw new UnloadData();
        }
        $this->logs = [];
        $this->updateLogFile();
    }


    public function getLogsNumber()
    {
        if (!$this->gotData) {
            throw new UnloadData();
        }
        return count($this->logs);
    }

    public function queryByDate(string $queryDate)
    {
        if(!$this->gotData){ throw new UnloadData();}
        $results = [];
        for ($i = 0; $i < $this->getLogsNumber(); $i++) {
            if ($this->logs[$i]->Date == $queryDate) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByError(bool $failed = true)
    {
        if(!$this->gotData){ throw new UnloadData();}
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

    public function queryByCode(int $errorCode)
    {
        if(!$this->gotData){ throw new UnloadData();}
        $results = [];
        for ($i = 0; $i < $this->getLogsNumber(); $i++) {
            if ($this->logs[$i]->ErrorCode == $errorCode) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByTime(string $timeOf, int $by = 0)
    {
        /**
         * @param by : 0 => Hours
         *             1 => Minutes
         *             2 => seconds
         * @param timeOf: The time value to query
         * @return array.
         */
        if(!$this->gotData){ throw new UnloadData();}
        $results = [];
        for ($i = 0; $i < count($this->logs); $i++) {
            $splitedHour = explode(":", $this->logs[$i]->Time);
            if ($splitedHour[$by] == $timeOf) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }

    public function queryByAction( string $action)
    {
        /**
         * 
         */
        if(!$this->gotData){ throw new UnloadData();}
        $results = [];
        for ($i = 0; $i < count($this->logs); $i++) {
            if ($this->logs[$i]->Action == $action) {
                array_push($results, $this->logs[$i]);
            } else { }
        }
        return $results;
    }
}

$systemLogConnection = new LoadLogFile("./logs/system.json");
global $systemLogConnection;
?>