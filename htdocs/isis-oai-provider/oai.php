<?php

$startdate = time();

require_once(APPLICATION_PATH . '/lib/parse_config.php');
require_once(APPLICATION_PATH . '/oai-metadataformats.php');
require_once(APPLICATION_PATH . '/lib/parse_databases.php');
require_once(APPLICATION_PATH . '/lib/functions.php');
require_once(APPLICATION_PATH . '/lib/OAIServer.php');
require_once(APPLICATION_PATH . '/lib/ISISItemFactory.php');
require_once(APPLICATION_PATH . '/lib/ISISItem.php');
require_once(APPLICATION_PATH . '/lib/ISISDb.php');

// default verb: Identify
$verb = ($_REQUEST['verb'] == "")? "Identify" : $_REQUEST['verb'];

// usado no lugar de ITem Factory, pois necessita se passado por referencia.
$item_factory = new ISISItemFactory($databases);

$repository_description = array(
    "Name" => $CONFIG['INFORMATION']['NAME'], // nome da bvs
    "AdminEmail" => array($CONFIG['INFORMATION']['EMAIL']), // email admin
    "EarliestDate" => $CONFIG['INFORMATION']['EARLIESTDATESTAMP'], 
    "DateGranularity" => "DATE", // ??
    "IDPrefix" =>  $CONFIG['INFORMATION']['IDPREFIX'], 
    "IDDomain" => $CONFIG['INFORMATION']['IDDOMAIN'], 
    "BaseURL" => "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'],
);

$server = new OAIServer($repository_description, &$item_factory, TRUE, 0);

foreach ($METADATAFORMATS as $name => $format ){
	$server->AddFormat($name, $format['TagName'], $format['SchemaNamespace'], $format['SchemaDefinition'],  $format['SchemaVersion'], $format['NamespaceList'], null, array() );	
}

$response = $server->GetResponse();
// show XML
header("Content-type: text/xml;");
print $response;

$enddate = time();
$time = $enddate - $startdate;

if(isset($_REQUEST['debug'])) {
	
	print "<!-- Execution Time: $time sec -->";
}


?>