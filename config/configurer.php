<?php

require_once "../Exceptions.php";
use ConfigErrors\InvalidDateFormat;
use ConfigErrors\InvalidHourFormat;

define("configFile", "configurations.json"); // always thinking on the / project folder
define("dateModes", ["Y/m/d", 'm/d/Y', 'd/m/Y']);
define('hourModes', ['H:i:s', 'i:H', 's:H:i', 'i:s:H']);
define("dftMonthsNames", ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Agost', 'September', 'October', 'November', 'December']);

class Configurations{
    public $document = [];
    public $sourceFile = "";
    
    public function __construct($source = configFile){
        $this->sourceFile = $source;
        $rawConfig = file_get_contents($source);
        $this ->document = json_decode($rawConfig);
    }

    public function configDate(string $newDateMode = "Y/m/d"){
        if(!in_array($newDateMode, dateModes)){ throw new InvalidDateFormat();}
        $this->document->DateConf = $newDateMode;
        $this->updateDocument();
    }

    public function configHour(string $newHourMode = "H:i:s"){
        if(!in_array($newHourMode, hourModes)){ throw new InvalidHourFormat();}
        $this->document->HourConf = $newHourMode;
        $this->updateDocument();
    }

    public function configMonths(){
        $newMonths = [];
        for($i = 0; $i<count(dftMonthsNames); $i++){
            $newValue = $_GET['month-num-'.($i+1)];
            $newMonths[$i] = $newValue;
        }
        $this->document->MonthNames = $newMonths;
        $this->updateDocument();
        delete($newMonths);
    }

    private function updateDocument(){
        $dumpedData = json_encode($this->document);
        file_put_contents($this->sourceFile, $dumpedData);
    }

    public function resetConfigurations(){
        $this->configDate();
        $this->configHour();
        $this->document->MonthsNames = array_values(dftMonthsNames);
        $this->updateDocument();
    }

}
$obj = new Configurations("./config/configurations.json");
$obj->resetConfigurations();

?>