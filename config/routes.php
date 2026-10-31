<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('@MichaelKaeferAdminGeneratorBundle/src/Controller/', 'attribute')
        ->prefix('/admin')
    ;
};
