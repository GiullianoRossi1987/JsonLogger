<?php
class Teste{
    public $teste = "aaa";

    public function show(){
        echo $this->teste;
    }
}
$obj = new Teste();
$obj->show();
?>