<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use Carbon\Carbon;

class MeshCtl{
    public $name;
    public function __construct($name){
        $this->name = $name;
    }

    private function writeOutputToLogFile($output){
        $logFilePath = '/var/www/wireguard-rest-api/log.txt';
        file_put_contents($logFilePath, $output . PHP_EOL, FILE_APPEND);
    }

    public function runContainer(){
        $cmd = "meshctl $this->name run";
        $line = trim(shell_exec($cmd));
        $this->writeOutputToLogFile($line);
        return $line;
    }

    public function getWireguardIP(){
        $cmd = "meshctl $this->name getIP";
        $line = trim(shell_exec($cmd));
        $this->writeOutputToLogFile($line);
        return $line;

    }

    public function stopAndRemove(){
        $cmd = "meshctl $this->name stop_remove";
        $line = trim(shell_exec($cmd));
        $this->writeOutputToLogFile($line);
        return $line;
    }

    public function listContainers(){
        $cmd = "meshctl $this->name list";
        $line = trim(shell_exec($cmd));
        $this->writeOutputToLogFile($line);
        return $line;
    }
    public function stopAndRemoveContainer(){

    }





    
}