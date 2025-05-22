<?php

require_once __DIR__.'/vendor/autoload.php';
require __DIR__.'/App/variables.php';

use Doctrine\Migrations\Provider\SchemaProvider;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Core\Env;
use Database\Entities\ActiveSessionsSchemaProvider;
use Database\Entities\BalancesSchemaProvider;
use Database\Entities\DevicesSchemaProvider;
use Database\Entities\FeatureSchemaProvider;
use Database\Entities\FileSchemaProvider;
use Database\Entities\FolderSchemaProvider;
use Database\Entities\LogSchemaProvider;
use Database\Entities\NotificationSchemaProvider;
use Database\Entities\PlanSchemaProvider;
use Database\Entities\ReferralSchemaProvider;
use Database\Entities\SettingSchemaProvider;
use Database\Entities\StorageSchemaProvider;
use Database\Entities\SubscriptionSchemaProvider;
use Database\Entities\TicketResponseSchemaProvider;
use Database\Entities\TicketSchemaProvider;
use Database\Entities\TokenSchemaProvider;
use Database\Entities\TransactionSchemaProvider;
use Database\Entities\UserSchemaProvider;
use Database\Entities\WalletSchemaProvider;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Symfony\Component\Console\Application;

Env::load();

$connection =  DriverManager::getConnection([
    'dbname' => Env::DB_NAME(),
    'user' => Env::DB_USER(),
    'password' => Env::DB_PASSWORD(),
    'host' => Env::DB_HOST(),
    'driver' => 'pdo_mysql',
]);

$config = new PhpFile('migrations.php');

$dependencyFactory = DependencyFactory::fromConnection($config, new ExistingConnection($connection));


$dependencyFactory->setDefinition(SchemaProvider::class, static fn () => new SubscriptionSchemaProvider());


$cli = new Application('Doctrine Migrations');
$cli->setCatchExceptions(true);
$cli->add(new DiffCommand($dependencyFactory));

$cli->run();