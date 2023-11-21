<?php

require 'vendor/autoload.php';

$dotenv = $dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// test that the variables are loaded:
echo getenv('OKTA_AUDIENCE');