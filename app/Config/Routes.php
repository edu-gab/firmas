<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

/*Rutas para AuthController*/
/* $routes->get('/account/login', 'AuthController::viewLogin');
$routes->get('/account/logout', 'AuthController::logout', ['filter' => 'authGuard']);
$routes->post('/__/auth', 'AuthController::auth');
 */
/*Rutas para EmpleadoController*/
$routes->post('/empleado/getAll', 'EmpleadoController::getInformacion', ['filter' => 'authGuard']);
$routes->post('/empleado/getData', 'EmpleadoController::getEmpleado', ['filter' => 'authGuard']);
$routes->post('/empleado/sendMail', 'EmpleadoController::sendMail', ['filter' => 'authGuard']);

/*Rutas para FirmaController*/
$routes->post('/firma/cargarFirma', 'FirmaController::cargarFirma'/* , ['filter' => 'authGuard'] */);
$routes->get('/firma/verificarColaboradoresNuevos', 'FirmaController::verificarColaboradoresNuevos', ['filter' => 'authGuard']);
$routes->post('/firma/sendRequestSignature', 'FirmaController::solicitarFirma', ['filter' => 'authGuard']);

/*Rutas para la UI*/
/* $routes->get('/', 'FirmaController::index', ['filter' => 'authGuard']);
$routes->get('/home', 'FirmaController::index', ['filter' => 'authGuard']); */


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
