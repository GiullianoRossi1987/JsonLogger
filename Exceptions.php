<?php

namespace LoggerErrors{

    use Exception;

    class InvalidFailureNumber extends Exception{
        public function errorMessage( int $failNumber){
            return "$failNumber is not a valid fail number! {line: ".$this->getLine()."}";
        }
    }

    class UnloadData extends Exception{
        public function errormMessage(){
            return 'The system cant do that action without the main log connection! {line: '.$this->getLine();'}';
        }
    }
}

namespace ConfigErrors{
    use Exception;

    class InvalidHourFormat extends Exception{
        public function errorMessage(string $hourFormat){
            return "'$hourFormat' is not a valid hour format! {line: ".$this->getLine()."}";
        }
    }

    class InvalidDateFormat extends Exception{
        public function errorMessage(string $dateFormat){
            return "'$dateFormat' is not a valid date format! {line: ".$this->getLine()."}";
        }
    }
}

?>