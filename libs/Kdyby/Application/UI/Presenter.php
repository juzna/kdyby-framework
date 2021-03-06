<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Application\UI;

use Kdyby;
use Kdyby\Templates\ITemplateFactory;
use Nette;
use Nette\Application\Responses;
use Nette\Diagnostics\Debugger;



/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 *
 * @property-read Kdyby\DI\Container $context
 * @property Kdyby\Templates\ITheme $theme
 */
abstract class Presenter extends Nette\Application\UI\Presenter
{

	/** @var Nette\Http\Context */
	private $httpContext;

	/** @var Nette\Application\Application */
	private $application;

	/** @var Nette\Http\Session */
	private $session;

	/** @var Nette\Http\User */
	private $user;

	/** @var \Kdyby\Templates\ITemplateFactory */
	private $templateFactory;



	/**
	 * @param \Kdyby\Templates\ITemplateFactory $templateFactory
	 */
	public function setTemplateFactory(ITemplateFactory $templateFactory)
	{
		$this->templateFactory = $templateFactory;
	}



	/**
	 * @param string|null $class
	 *
	 * @return \Kdyby\Templating\Template
	 */
	protected function createTemplate($class = NULL)
	{
		if ($this->templateFactory === NULL) {
			return parent::createTemplate($class);
		}

		return $this->templateFactory->createTemplate($this, $class);
	}



	/**
	 * @return \Nette\Http\Request
	 */
	protected function getHttpRequest()
	{
		return $this->getHttpContext()->getRequest();
	}



	/**
	 * @return \Nette\Http\Response
	 */
	protected function getHttpResponse()
	{
		return $this->getHttpContext()->getResponse();
	}



	/**
	 * @param \Nette\Http\Context $httpContext
	 */
	public function setHttpContext(Nette\Http\Context $httpContext)
	{
		$this->httpContext = $httpContext;
	}



	/**
	 * @return \Nette\Http\Context
	 */
	protected function getHttpContext()
	{
		if ($this->httpContext !== NULL) {
			return $this->httpContext;
		}

		return parent::getHttpContext();
	}



	/**
	 * @param \Nette\Application\Application $application
	 */
	public function setApplication(Nette\Application\Application $application)
	{
		$this->application = $application;
	}



	/**
	 * @return \Nette\Application\Application
	 */
	public function getApplication()
	{
		if ($this->application !== NULL) {
			return $this->application;
		}

		return parent::getApplication();
	}



	/**
	 * @param \Nette\Http\Session $session
	 */
	public function setSession(Nette\Http\Session $session)
	{
		$this->session = $session;
	}



	/**
	 * @param string|NULL $namespace
	 *
	 * @return \Nette\Http\Session
	 */
	public function getSession($namespace = NULL)
	{
		if ($this->session !== NULL) {
			return $namespace === NULL ? $this->session : $this->session->getSection($namespace);
		}

		return parent::getSession($namespace);
	}



	/**
	 * @param \Nette\Http\User $user
	 */
	public function setUser(Nette\Http\User $user)
	{
		$this->user = $user;
	}



	/**
	 * @return \Nette\Http\User
	 */
	public function getUser()
	{
		if ($this->user !== NULL) {
			return $this->user;
		}

		return $this->getContext()->user;
	}



	/**
	 * Sends AJAX payload to the output.
	 *
	 * @param array|object|null $payload
	 *
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function sendPayload($payload = NULL)
	{
		if ($payload !== NULL) {
			$this->sendResponse(new Responses\JsonResponse($payload));
		}

		parent::sendPayload();
	}



	/**
	 * If Debugger is enabled, print template variables to debug bar
	 */
	protected function afterRender()
	{
		parent::afterRender();
		Kdyby\Diagnostics\TemplateParametersPanel::register($this);
	}

}
