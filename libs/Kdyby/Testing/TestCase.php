<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Testing;

use Kdyby;
use Nette;
use Nette\ObjectMixin;



/**
 * @author Filip Procházka
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{

	/** @var Kdyby\DI\Container */
	private $context;

	/** @var Kdyby\DI\Configurator */
	private $configurator;

	/** @var TempClassGenerator */
	private $tempClassGenerator;



	/**
	 * @param string $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '')
	{
		$this->configurator = Nette\Environment::getConfigurator();
		$this->context = $this->configurator->getContainer();
		parent::__construct($name, $data, $dataName);
	}



	/**
	 * @return Kdyby\DI\Configurator
	 */
	protected function getConfigurator()
	{
		return $this->configurator;
	}



	/**
	 * @return Kdyby\DI\Container
	 */
	public function getContext()
	{
		return $this->context;
	}



	/**
	 * @param array|\Nette\Callback|\Closure $callback
	 * @param Nette\Object $object
	 * @param string $eventName
	 * @param int|NULL $count
	 */
	public function assertEventHasCallback($callback, $object, $eventName, $count = NULL)
	{
		$this->assertInstanceOf('Nette\Object', $object, 'Object supports events');
		$this->assertObjectHasAttribute($eventName, $object, 'Object has event');

		// deeply extract callback
		$extractCallback = function ($callback) use (&$extractCallback) {
			if ($callback instanceof Nette\Callback) {
				return $extractCallback($callback->getNative());
			}
			return callback($callback);
		};

		$event = array_map($extractCallback, $object->$eventName);
		$this->assertNotEmpty($event, 'Event contains listeners');

		$callback = $extractCallback($callback);
		$targets = array_filter($event, function ($target) use ($callback) {
			return $target == $callback;
		});
		$this->assertNotNull($targets, 'Similar listener is in event');

		if ($count !== NULL) {
			$this->assertEquals($count, count($targets), 'Listener is in stack ' . $count . ' times');
		}
	}


	/********************* TempClassGenerator *********************/


	/**
	 * @return TempClassGenerator
	 */
	private function getTempClassGenerator()
	{
		if ($this->tempClassGenerator === NULL) {
			$this->tempClassGenerator = new TempClassGenerator($this->getContext()->expand('%tempDir%/cache'));
		}

		return $this->tempClassGenerator;
	}



	/**
	 * @param string $class
	 * @return string
	 */
	protected function touchTempClass($class = NULL)
	{
		return $this->getTempClassGenerator()->generate($class);
	}



	/**
	 * @param string $class
	 * @return string
	 */
	protected function resolveTempClassFilename($class)
	{
		return $this->getTempClassGenerator()->resolveFilename($class);
	}


	/********************* Exceptions handling *********************/


	/**
	 * This method is called when a test method did not execute successfully.
	 *
	 * @param Exception $e
	 * @since Method available since Release 3.4.0
	 */
	protected function onNotSuccessfulTest(\Exception $e)
	{
		Nette\Diagnostics\Debugger::log($e);
		parent::onNotSuccessfulTest($e);
	}


	/********************* Nette\Object behaviour ****************d*g**/



	/**
	 * @return Nette\Reflection\ClassType
	 */
	public /**/static/**/ function getReflection()
	{
		return new Nette\Reflection\ClassType(/*5.2*$this*//**/get_called_class()/**/);
	}



	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}



	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}



	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}



	public function __unset($name)
	{
		ObjectMixin::remove($this, $name);
	}

}