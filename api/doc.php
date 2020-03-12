<?php

require("../vendor/autoload.php");
$openApi = \OpenApi\scan(__DIR__.'/');
header('Content-Type: application/x-yaml');
echo $openApi->toYaml();