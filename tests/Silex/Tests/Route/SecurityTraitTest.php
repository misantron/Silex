<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silex\Tests\Route;

use PHPUnit\Framework\TestCase;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;

/**
 * SecurityTrait test cases.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SecurityTraitTest extends TestCase
{
    public function testSecureWithNoAuthenticatedUser()
    {
        $app = $this->createApplication();

        $app->get('/', function () { return 'foo'; })
            ->secure('ROLE_ADMIN')
        ;

        $request = Request::create('/');
        $response = $app->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testSecureWithAuthorizedRoles()
    {
        $app = $this->createApplication();

        $app->get('/', function () { return 'foo'; })
            ->secure('ROLE_ADMIN')
        ;

        $request = Request::create('/');
        $request->headers->set('PHP_AUTH_USER', 'fabien');
        $request->headers->set('PHP_AUTH_PW', 'foo');
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSecureWithUnauthorizedRoles()
    {
        $app = $this->createApplication();

        $app->get('/', function () { return 'foo'; })
            ->secure('ROLE_SUPER_ADMIN')
        ;

        $request = Request::create('/');
        $request->headers->set('PHP_AUTH_USER', 'fabien');
        $request->headers->set('PHP_AUTH_PW', 'foo');
        $response = $app->handle($request);
        $this->assertEquals(403, $response->getStatusCode());
    }

    private function createApplication()
    {
        $app = new Application();
        $app['route_class'] = SecurityRoute::class;
        $app->register(new SecurityServiceProvider(), [
            'security.firewalls' => [
                'default' => [
                    'http' => true,
                    'users' => [
                        'fabien' => ['ROLE_ADMIN', 'foo'],
                    ],
                ],
            ],
            'security.default_encoder' => function ($app) {
                return new PlaintextPasswordEncoder();
            },
        ]);

        return $app;
    }
}
