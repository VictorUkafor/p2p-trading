<?php
require("../vendor/autoload.php");
$openapi = \OpenApi\scan(__DIR__.'/../app/Http/Controllers/API');
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();