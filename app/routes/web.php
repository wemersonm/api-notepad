<?php

use app\routes\Routers;

$router = new Routers;


$router->add("/auth", 'POST', 'AuthController@login');
$router->add("/register/", "POST", "AuthController@store");
$router->add("/notes", "GET", "NotesController@notes")->middlewares(['auth']);
$router->add("/notes", "POST", "NotesController@store")->middlewares(['auth']);
$router->add("/notes/(:numeric)", "GET", "NotesController@show", ['idNotes'])->middlewares(['auth']);
$router->add("/notes/(:numeric)", "PUT", "NotesController@update", ['idNotes'])->middlewares(['auth']);
$router->add("/notes/(:numeric)", "DELETE", "NotesController@destroy", ['idNotes'])->middlewares(['auth']);
