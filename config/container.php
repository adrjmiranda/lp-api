<?php

use DI\Container;
use Slim\Factory\AppFactory;

$container = new Container();
AppFactory::setContainer($container);

require_once __DIR__ . "/containers/logger.php";
require_once __DIR__ . "/containers/responses.php";
require_once __DIR__ . "/containers/services.php";
require_once __DIR__ . "/containers/template.php";
require_once __DIR__ . "/containers/validator.php";








