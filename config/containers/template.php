<?php

use Latte\Engine;
use Latte\Loaders\FileLoader;
use LpApi\Helpers\App;

$container->set(Engine::class, function (): Engine {
  $latte = new Engine();

  $tempDir = App::rootPath() . '/temp/cache';
  $templateDir = App::rootPath() . '/templates';

  if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
  }

  if (!is_dir($templateDir)) {
    mkdir($templateDir, 0777, true);
  }

  $latte->setTempDirectory($tempDir);
  $latte->setLoader(new FileLoader($templateDir));

  return $latte;
});