<?php
require_once __DIR__.'/vendor/autoload.php';

use Core\Env;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Tools\Console\Command;
use Symfony\Component\Console\Application;

Env::load();
$connection =  DriverManager::getConnection([
    'dbname' => Env::DB_NAME(),
    'user' => Env::DB_USER(),
    'password' => Env::DB_PASSWORD(),
    'host' => Env::DB_HOST(),
    'driver' => 'pdo_mysql',
    'charset' => 'utf8mb4',
]);

$config = new PhpFile('migrations.php'); // Or use one of the Doctrine\Migrations\Configuration\Configuration\* loaders

$dependencyFactory = DependencyFactory::fromConnection($config, new ExistingConnection($connection));

$cli = new Application('Doctrine Migrations');
$cli->setCatchExceptions(true);

$cli->addCommands(array(
    new Command\DumpSchemaCommand($dependencyFactory),
    new Command\ExecuteCommand($dependencyFactory),
    new Command\GenerateCommand($dependencyFactory),
    new Command\LatestCommand($dependencyFactory),
    new Command\ListCommand($dependencyFactory),
    new Command\MigrateCommand($dependencyFactory),
    new Command\RollupCommand($dependencyFactory),
    new Command\StatusCommand($dependencyFactory),
    new Command\SyncMetadataCommand($dependencyFactory),
    new Command\VersionCommand($dependencyFactory),
));

$cli->run();