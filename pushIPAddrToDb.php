<?php
require 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/Wireguard.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/IPNetwork.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/api/lib/MeshCtl.class.php';



$ip = new IPNetwork('172.20.0.0/16','wg0');
print_r($ip->syncNetworkFile());

