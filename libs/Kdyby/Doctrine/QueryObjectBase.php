<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Doctrine;

use Doctrine;
use DoctrineExtensions\Paginate\Paginate;
use Kdyby;
use Kdyby\Persistence\IQueryable;
use Nette;
use Nette\Utils\Paginator;



/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
abstract class QueryObjectBase implements Kdyby\Persistence\IQueryObject
{

	/** @var \Nette\Utils\Paginator */
	private $paginator;

	/** @var \Doctrine\ORM\Query */
	private $lastQuery;



	/**
	 * @param \Nette\Utils\Paginator $paginator
	 */
	public function __construct(Paginator $paginator = NULL)
	{
		$this->paginator = $paginator;
	}



	/**
	 * @return \Nette\Utils\Paginator
	 */
	public function getPaginator()
	{
		return $this->paginator;
	}



	/**
	 * @param \Kdyby\Persistence\IQueryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected abstract function doCreateQuery(IQueryable $repository);



	/**
	 * @param \Kdyby\Persistence\IQueryable $repository
	 * @return \Doctrine\ORM\Query
	 */
	protected function getQuery(IQueryable $repository)
	{
		$query = $this->doCreateQuery($repository);
		if ($query instanceof Doctrine\ORM\QueryBuilder) {
			return $this->lastQuery = $query->getQuery();

		} elseif ($query instanceof Doctrine\ORM\Query) {
			return $this->lastQuery = $query;
		}

		$class = $this->getReflection()->getMethod('doCreateQuery')->getDeclaringClass();
		throw new Kdyby\UnexpectedValueException("Method " . $class . "::doCreateQuery() must return" .
				" instanceof Doctrine\\ORM\\Query or instanceof Doctrine\\ORM\\QueryBuilder, " .
				Kdyby\Tools\Mixed::getType($query) . " given.");
	}



	/**
	 * @param \Kdyby\Persistence\IQueryable $repository
	 *
	 * @return integer
	 */
	public function count(IQueryable $repository)
	{
		return Paginate::getTotalQueryResults($this->getQuery($repository));
	}



	/**
	 * @param \Kdyby\Persistence\IQueryable $repository
	 * @return array
	 */
	public function fetch(IQueryable $repository)
	{
		$query = $this->getQuery($repository);

		if ($this->paginator) {
			$query = Paginate::getPaginateQuery($query, $this->paginator->getOffset(), $this->paginator->getLength()); // Step 2 and 3

		} else {
			$query = $query->setMaxResults(NULL)->setFirstResult(NULL);
		}

		$this->lastQuery = $query;
		return $query->getResult();
	}



	/**
	 * @param \Kdyby\Persistence\IQueryable $repository
	 * @return object
	 */
	public function fetchOne(IQueryable $repository)
	{
		$query = $this->getQuery($repository)
			->setFirstResult(NULL)
			->setMaxResults(1);

		$this->lastQuery = $query;
		return $query->getSingleResult();
	}



	/**
	 * @internal For Debugging purposes only!
	 * @return \Doctrine\ORM\Query
	 */
	public function getLastQuery()
	{
		return $this->lastQuery;
	}

}
