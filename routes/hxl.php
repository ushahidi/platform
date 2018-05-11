<?php

if (Features::isEnabled('hxl')) {
    $router->get('hxl', "HXLController@index");
}
