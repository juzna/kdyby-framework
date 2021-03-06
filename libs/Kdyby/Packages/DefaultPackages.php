<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Packages;

use Kdyby;
use Nette;



/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
class DefaultPackages extends Kdyby\Package\DirectoryPackages
{

    /**
     */
    public function __construct()
    {
        parent::__construct(__DIR__, __NAMESPACE__);
    }

}