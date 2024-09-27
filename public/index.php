<?php
require __DIR__ . '/../vendor/autoload.php';
use Polidog\DogPress\Application;
use Polidog\DogPress\Config;

Application::run(new Config());