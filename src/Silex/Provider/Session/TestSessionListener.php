<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Silex\Provider\Session;

use Pimple\Container;

/**
 * Simulates sessions for testing purpose.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TestSessionListener extends \Symfony\Component\HttpKernel\EventListener\TestSessionListener
{
    public function __construct(Container $app)
    {
        $container = new \Pimple\Psr11\Container($app);

        parent::__construct($container);
    }
}
