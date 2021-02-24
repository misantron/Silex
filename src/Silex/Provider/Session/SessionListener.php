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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Sets the session in the request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SessionListener extends \Symfony\Component\HttpKernel\EventListener\SessionListener
{
    public function __construct(Container $app, bool $debug = false)
    {
        $container = new \Pimple\Psr11\Container($app);

        parent::__construct($container, $debug);
    }

    protected function getSession(): ?SessionInterface
    {
        return $this->container->has('session')
            ? $this->container->get('session')
            : null;
    }
}
