<?php
include "./config/configurer.php";
define("identGuide", "    ");
//////////////  Exceptions
class InvalidFailuresNumber extends Exception{
    private $errno = 'Invalid failures number received!';

    public function __construct(){
        echo $errno;
        die();
    }
}

class InvalidAction extends Exception{
    private $errno = 'Invalid Action found!';
}


/////////////   Classes

class LoadLogFile{
    public $source = "";
    public $logs   = [];
    protected $gotData = false;
    private $objConfig;

    private function getConfig(){ $this->objConfig = new Configurations("./config/configurations.json");}

    public function __construct($sourceFile){
        $this->source = $sourceFile;
        $this->logs = json_decode(file_get_contents($sourceFile));
        $this->gotData = true;
        $this->getConfig();
    }

    private function updateLogFile(){
        if(!$this->gotData){ throw new InvalidAction();}
        $rawData = json_encode($this->logs);
        file_put_contents($this->source, $rawData);
    }

    public function listLogs($onWeb = false){
        if(!$this->gotData){ throw new InvalidAction();}
        $rtData = "";
        if($onWeb){ 
            $rtData = $rtData . "<table>Logs From \n";
            for($i = 0; $i < count($this->logs); $i++){
                // set the headers
                $rtData = $rtData . "<th>\n";
                foreach($this->logs[$i] as $key => $value){
                    $rtData = $rtData . "<td>".$key."</td>\n";
                }
                $rtData = $rtData . "</th>\n";
                $rtData = $rtData . "<tr>\n";
                foreach($this->logs[$i] as $header => $value){
                    $rtData = $rtData . "<td>".$value."</td>\n";
                }
                $rtData = $rtData . "</tr>\n";
            }
            $rtData = $rtData . "</table>\n";
        }
        else{
            for($i = 0; $i < count($this->logs); $i++){
                $rtData = $rtData."Log Number ".$i."\n";
                foreach($this->logs[$i] as $key => $value){
                    $rtData = $rtData.identGuide."$key => $value\n";
                }
            }
        }
        return $rtData;
        
    }

    public function addLog($action, $failures = 0, $failureCode = 0){
        if(!$this->gotData){ throw new InvalidAction();}
        $date = date($this->objConfig->document->DateConf);
        $hour = date($this->objConfig->document->HourConf);
        array_push($this->logs, Array(
            'Date' => $date,
            'Time' => $hour,
            'Action' => $action,
            'Failed' => $failures,
            'ErrorCode' => $failureCode 
        ));
        $this->updateLogFile();
    }   

    public function clearLogs(){
        if(!$this->gotData){ throw new InvalidAction();}
        $this->logs = [];
        $this->updateLogFile();
    }


    public function getLogsNumber(){ 
        if(!$this->gotData){throw new InvalidAction();}
        return count($this->logs); 
    }
}

$logFile = new LoadLogFile("./logs/example-log.json");
echo $logFile->getLogsNumber();

?>