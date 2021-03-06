<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\DI;

use Symfony;
use Symfony\Component\Console;
use Doctrine;
use Kdyby;
use Kdyby\Caching\CacheServices;
use Nette;



/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 *
 * @property-read Doctrine\ORM\EntityManager $entityManager
 * @property-read Doctrine\Common\EventManager $ormEventManager
 * @property-read Doctrine\ORM\Configuration $ormConfiguration
 * @property-read Doctrine\DBAL\Connection $dbalConnection
 * @property-read Kdyby\Doctrine\Mapping\Driver\AnnotationDriver $ormMetadataDriver
 * @property-read Doctrine\ORM\Tools\SchemaTool $ormSchemaTool
 * @property-read Kdyby\Doctrine\Diagnostics\Panel $sqlLogger
 * @property-read Kdyby\Doctrine\Cache $ormCache
 * @property-read Doctrine\Common\Annotations\AnnotationReader $annotationReader
 *
 * @property-read Nette\Application\Application $application
 * @property-read Nette\Application\IPresenterFactory $presenterFactory
 * @property-read Kdyby\Application\ModuleCascadeRegistry $moduleRegistry
 * @property-read Kdyby\Application\RequestManager $requestManager
 * @property-read Kdyby\Config\Settings $settings
 *
 * @property-read Console\Application $console
 * @property-read Console\Helper\HelperSet $consoleHelpers
 * @property-read Kdyby\Tools\FreezableArray $consoleCommands
 *
 * @property-read Nette\Application\IRouter $router
 * @property-read Nette\Http\Request $httpRequest
 * @property-read Nette\Http\Response $httpResponse
 * @property-read Nette\Http\Context $httpContext
 * @property-read Nette\Http\Session $session
 *
 * @property-read Nette\Http\User $user
 * @property-read Kdyby\Security\Users $users
 *
 * @property-read Kdyby\Templates\ITemplateFactory $templateFactory
 * @property-read Nette\Caching\Storages\PhpFileStorage $templateCacheStorage
 * @property-read Nette\Latte\Engine $latteEngine
 *
 * @property-read Nette\Loaders\RobotLoader $robotLoader
 *
 * @property-read Kdyby\Doctrine\Mapping\TypeMapper $doctrineTypeMapper
 * @property-read Kdyby\Doctrine\Mapping\EntityValuesMapper $doctrineEntityValuesMapper
 * @property-read Kdyby\Forms\Mapping\EntityFormMapperFactory $entityFormMapperFactory
 * @property-read Kdyby\Forms\EntityFormFactory $entityFormFactory
 *
 * @property-read Nette\Caching\IStorage $cacheStorage
 * @property-read Nette\Caching\Storages\IJournal $cacheJournal
 *
 * @property-read Nette\Mail\IMailer $mailer
 *
 * @property-read Kdyby\Modules\InstallWizard $installWizard
 */
class SystemContainer extends Container
{

	/** @var \Kdyby\Caching\CacheServices */
	private $cacheServices;



	/**
	 * @param \Kdyby\Caching\CacheServices $cache
	 */
	public function setCacheServices(CacheServices $cache)
	{
		$this->cacheServices = $cache;
	}



	/**
	 * @return Nette\Caching\IStorage
	 */
	protected function getCacheStorage()
	{
		return $this->cacheServices->cacheStorage;
	}



	/**
	 * @return Nette\Caching\Storages\IJournal
	 */
	protected function getCacheJournal()
	{
		return $this->cacheServices->cacheJournal;
	}



	/**
	 * @return Nette\Caching\IStorage
	 */
	protected function getPhpFileStorage()
	{
		return $this->cacheServices->phpFileStorage;
	}

}