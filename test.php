<?php
require 'vendor/autoload.php';
// use MongoDB\Operation\BulkWrite;

require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/Wireguard.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/IPNetwork.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/MeshCtl.class.php';



$a = new MeshCtl('jerlin2');

$b = $a->runContainer();



// print_r($wg->getCIDR());
 
// print_r($ip->getNetwork());


// $ip = new IPNetwork('172.20.0.0/16','wg0');
// print_r($ip->syncNetworkFile());

// print_r($ip->getNextIP());