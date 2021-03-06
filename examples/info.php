<?php

// to not repeat all that stuff
include('quickstart.php');

// server and port
$server = '127.0.0.1';
$port = 7171;

// queries server of status info
$info = new OTS_ServerInfo($server, $port);
$status = $info->status();

// offline
if(!$status)
{
    echo 'Server ', $server, ' is offline.', "\n";
}
// displays various info
else
{
    echo 'Server name: ', $status->getName(), "\n";
    echo 'Server owner: ', $status->getOwner(), "\n";
    echo 'Players online: ', $status->getOnlinePlayers(), "\n";
    echo 'Maximum allowed number of players: ', $status->getMaxPlayers(), "\n";
    echo 'Required client version: ', $status->getClientVersion(), "\n";
    echo 'All monsters: ', $status->getMonstersCount(), "\n";
    echo 'Server message: ', $status->getMOTD(), "\n";
}

?>
