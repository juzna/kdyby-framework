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
use Kdyby;
use Nette;



/**
 * @author Filip Procházka
 */
interface IQueryable
{

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @return Doctrine\ORM\QueryBuilder|Doctrine\CouchDB\View\AbstractQuery $qb
     */
    function createQueryBuilder();

}