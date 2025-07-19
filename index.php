<?php

require_once __DIR__ . "/bootstrap.php";

use LpApi\Helpers\App;

// routes
require_once App::rootPath() . "/src/routes/mailer.php";

// run application
$app->run();