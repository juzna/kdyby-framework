<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\DI;

use Doctrine\DBAL\Tools\Console\Command as DbalCommand;
use Doctrine\ORM\Tools\Console\Command as OrmCommand;
use Kdyby;
use Kdyby\Application\ModuleCascadeRegistry;
use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\UI\Presenter;
use Nette\DI;
use Symfony\Component\Console;



/**
 * @author Patrik Votoček
 * @author Filip Procházka
 */
class Configurator extends Nette\Configurator
{

	/**
	 * @param string $containerClass
	 */
	public function __construct($containerClass = 'Kdyby\DI\Container')
	{
		parent::__construct($containerClass);
	}



	/**
	 * @param DI\Container $container
	 * @param array $options
	 * @return Kdyby\Application\Application
	 */
	public static function createServiceApplication(DI\Container $container, array $options = NULL)
	{
		$context = new Kdyby\DI\Container;
		$context->addService('httpRequest', $container->httpRequest);
		$context->addService('httpResponse', $container->httpResponse);
		$context->addService('session', $container->session);
		$context->addService('presenterFactory', $container->presenterFactory);
		$context->addService('router', $container->router);
		$context->addService('console', $container->console);

		Presenter::$invalidLinkMode = $container->getParam('productionMode', TRUE)
			? Presenter::INVALID_LINK_SILENT : Presenter::INVALID_LINK_WARNING;

		$application = new Kdyby\Application\Application($context);
		$application->catchExceptions = $container->getParam('productionMode', TRUE);
		$application->errorPresenter = 'Error';

		$container->params['baseUrl'] = $baseUrl = rtrim($container->httpRequest->getUrl()->getBaseUrl(), '/');
		$container->params['basePath'] = preg_replace('#https?://[^/]+#A', '', $baseUrl);

		return $application;
	}



	/**
	 * @param DI\Container $container
	 * @return Kdyby\Application\RequestManager
	 */
	public static function createServiceRequestManager(DI\Container $container)
	{
		return new Kdyby\Application\RequestManager($container->application);
	}



	/**
	 * @param DI\Container $container
	 * @return Kdyby\Doctrine\Container
	 */
	public static function createServiceDoctrine(DI\Container $container)
	{
		return new Kdyby\Doctrine\Container($container);
	}



	/**
	 * @param DI\Container $container
	 * @return Nette\Application\IPresenterFactory
	 */
	public static function createServicePresenterFactory(DI\Container $container)
	{
		return new Kdyby\Application\PresenterFactory($container->moduleRegistry, $container);
	}



	/**
	 * @param DI\Container $container
	 * @return Kdyby\Templates\ITemplateFactory
	 */
	public static function createServiceTemplateFactory(DI\Container $container)
	{
		return new Kdyby\Templates\TemplateFactory($container->latteEngine);
	}



	/**
	 * @param DI\Container $container
	 * @return Nette\Latte\Engine
	 */
	public static function createServiceLatteEngine(DI\Container $container)
	{
		$engine = new Nette\Latte\Engine;

		foreach ($container->getParam('macros', array()) as $macroSet) {
			call_user_func(callback($macroSet), $engine->parser);
		}

		return $engine;
	}



	/**
	 * @param DI\Container $container
	 * @return ModuleCascadeRegistry
	 */
	public static function createServiceModuleRegistry(DI\Container $container)
	{
		$register = new ModuleCascadeRegistry;
		$register->add('Kdyby\Modules', KDYBY_DIR . '/Modules');

		foreach ($container->getParam('modules', array()) as $namespace => $path) {
			$register->add($namespace, $container->expand($path));
		}

		return $register;
	}



	/**
	 * @param DI\Container $container
	 * @return Nette\Application\Routers\RouteList
	 */
	public static function createServiceRouter(DI\Container $container)
	{
		$router = new Nette\Application\Routers\RouteList;

		$router[] = new Route('index.php', array(
			'module' => 'Front',
			'presenter' => 'Homepage',
			'action' => 'default',
		), Route::ONE_WAY);

		$router[] = new Route('<presenter>/<action>[/<id>]', array(
			'module' => 'Front',
			'presenter' => 'Homepage',
			'action' => 'default',
		));

		return $router;
	}



	/**
	 * @param DI\IContainer $container
	 * @return Console\Helper\HelperSet
	 */
	public static function createServiceConsoleHelpers(DI\IContainer $container)
	{
		$helperSet = new Console\Helper\HelperSet;
		$helperSet->set(new ContainerHelper($container), 'container');
		$helperSet->set(new Kdyby\Doctrine\EntityManagerHelper($container), 'em');

		return $helperSet;
	}



	/**
	 * @param DI\IContainer $container
	 * @return Kdyby\Tools\FreezableArray
	 */
	public static function createServiceConsoleCommands(DI\IContainer $container)
	{
		return new Kdyby\Tools\FreezableArray(array(
			// DBAL Commands
			new DbalCommand\RunSqlCommand(),
			new DbalCommand\ImportCommand(),

			// ORM Commands
			new OrmCommand\SchemaTool\CreateCommand(),
			new OrmCommand\SchemaTool\UpdateCommand(),
			new OrmCommand\SchemaTool\DropCommand(),
			new OrmCommand\GenerateProxiesCommand(),
			new OrmCommand\RunDqlCommand(),
		));
	}



	/**
	 * @param DI\IContainer $container
	 * @return Console\Application
	 */
	public static function createServiceConsole(DI\Container $container)
	{
		$name = Kdyby\Framework::NAME . " Command Line Interface";
		$cli = new Console\Application($name, Kdyby\Framework::VERSION);

		$cli->setCatchExceptions(TRUE);
		$cli->setHelperSet($container->consoleHelpers);
		$cli->addCommands($container->consoleCommands->freeze()->iterator->getArrayCopy());

		return $cli;
	}



	/**
	 * @return Kdyby\Http\User
	 */
	public static function createServiceUser(DI\Container $container)
	{
		$context = new DI\Container;
		// copies services from $container and preserves lazy loading
		$context->addService('authenticator', function() use ($container) {
			return $container->authenticator;
		});
		$context->addService('authorizator', function() use ($container) {
			return $container->authorizator;
		});
		$context->addService('session', $container->session);

		return new Kdyby\Http\User($context);
	}



	/**
	 * @param DI\Container $container
	 * @return Kdyby\Components\Grinder\GridFactory
	 */
	public static function createServiceGrinderFactory(DI\Container $container)
	{
		return new Kdyby\Components\Grinder\GridFactory($container->doctrine->entityManager, $container->session);
	}

}