<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import('@MichaelKaeferAdminUiBundle/src/Controller/', 'attribute')
        ->prefix('/admin')
    ;
};
