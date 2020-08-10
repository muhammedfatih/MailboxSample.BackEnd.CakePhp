<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use App\Middleware\AuthorizationMiddleware;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);
Router::scope('/api', function (RouteBuilder $routes) {
    // Prior to 3.5.0 use `extensions()`
    $routes->setExtensions(['json']);
    $routes->resources('Users', 
        [
            'path' => 'user',
            'only' => ['create', 'login'],
            'map' => [
                'login' => [
                    'action' => 'login',
                    'method' => 'POST',
                    'path' => 'login'
                ]
            ]
        ]
    );
});
Router::scope('/api', function (RouteBuilder $routes) {
    $routes->registerMiddleware('auth', new AuthorizationMiddleware());
    $routes->applyMiddleware('auth');
    $routes->resources('Mails', 
        [
            'path' => 'mail',
            'only' => ['create', 'read', 'unreads', 'numberOfUnreads', 'list'],
            'map' => [
                'read' => [
                    'action' => 'read',
                    'method' => 'GET',
                    'path' => ':guid'
                ],
                'unreads' => [
                    'action' => 'unreads',
                    'method' => 'GET',
                    'path' => 'unreads'
                ],
                'numberOfUnreads' => [
                    'action' => 'numberOfUnreads',
                    'method' => 'GET',
                    'path' => 'numberOfUnreads'
                ],
                'list' => [
                    'action' => 'list',
                    'method' => 'GET',
                    'path' => ':page/:pageSize'
                ],
            ]
        ]
    );
});
Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->options('/api/user', []);
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
    $routes->fallbacks(DashedRoute::class);
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();