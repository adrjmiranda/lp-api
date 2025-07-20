<?php

use LpApi\Helpers\App;

require_once __DIR__ . "/bootstrap.php";

// routes
require_once App::rootPath() . "/src/routes/mailer.php";

// run application
$app->run();