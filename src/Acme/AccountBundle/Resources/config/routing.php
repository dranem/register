<?php
// src/Acme/AccountBundle/Resources/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('account_register', new Route('/register', array(
    '_controller' => 'AcmeAccountBundle:Account:register',
)));
$collection->add('account_create', new Route('/register/create', array(
    '_controller' => 'AcmeAccountBundle:Account:create',
)));

return $collection;