<?php
use Nette\Application\Routers\Route,
        Nette\Application\Routers\RouteList,
        Nette\Application\Routers\SimpleRouter;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

// Setup router using mod_rewrite detection
if (!function_exists('apache_get_modules') || !in_array('mod_rewrite', apache_get_modules())) 
{
        $container->addService('router', new SimpleRouter('Front:Default:default'));
}

return $container;
