<?php

use ATEKA\CurlRequest;
use ATEKA\Autoload;

require __DIR__ . '/src/Autoload.php';
require __DIR__ . '/src/parameters.php';
Autoload::register();

$resquest = new CurlRequest();
$resquest->prepare('http://api.wunderground.com/api/'. WEATHER_UNDERGROUND_KEY . '/conditions/q/CA/San_Francisco.json');
$resquest->setRequestContent('json');
$result = $resquest->execute(); ?>

<?= '<pre>' ?>
<?php var_dump(json_decode($result)); ?>
<?= '</pre>' ?>


<?= '<pre>' ?>
<?php var_dump($resquest->getResponseHeader()); ?>
<?= '</pre>' ?>
