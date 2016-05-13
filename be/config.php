<?php

define('ESLINT_OUTPUT_FILE', '/tmp/result.json');
define('NODE', '/home/on/.nvm/versions/node/v0.12.9/bin/');

require_once __DIR__ . '/vendor/autoload.php';

ERS\Db::obtain('localhost', 'root', 'KronuS', 'eslint', '')->connectPDO();