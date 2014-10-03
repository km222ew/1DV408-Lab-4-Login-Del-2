<?php

session_set_cookie_params(0);
session_start();

//Autoloader?
require_once('src/app/account/Model/account.model.php');
require_once('src/app/account/Model/account.registerModel.php');
require_once('src/app/account/Controller/account.controller.php');
require_once('src/app/account/View/account.view.php');
require_once('src/app/account/View/account.registerView.php');
require_once('src/components/response/response.php');
require_once('src/components/cookie/cookie.service.php');
require_once('src/components/notify/notify.service.php');
require_once('src/components/notify/notify.view.php');
require_once('src/components/notify/notification.php');

$cookieService = new CookieService();
$response = new Response();
$notify = new Notify();
$notifyView = new NotifyView($notify);
$model = new AccountModel($notify);
$registerModel = new AccountRegisterModel($notify);
$view = new AccountView($model, $cookieService);
$registerView = new AccountRegisterView($cookieService);
$controller = new AccountController($model, $registerModel, $view, $registerView);

$response->HTMLPage($controller->index(), $notifyView);