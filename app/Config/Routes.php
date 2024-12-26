<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('board', 'Board::index');
$routes->get('board/view/(:num)', 'Board::view/$1');
$routes->get('board/download/(:num)', 'Board::download/$1');
$routes->get('login', 'Board::login');
$routes->post('login', 'Board::loginAction');
$routes->get('logout', 'Board::logout');
$routes->get('board/write/', 'Board::write');
$routes->post('board/write/', 'Board::writeAction');
$routes->get('board/modify/(:num)', 'Board::modify/$1');
$routes->post('board/modify/(:num)', 'Board::modifyAction/$1');
$routes->get('file/download/(:num)', 'FileController::download/$1');
$routes->delete('board/delete/(:num)', 'Board::delete/$1');
$routes->post('file/delete', 'FileController::deleteFile');