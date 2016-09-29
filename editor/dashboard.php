<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 28.09.2016
     * Time: 21:48
     */
    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../libs/dwoo/lib/Dwoo/Autoloader.php'; //Dwoo Laden
    require_once '../classes/User.php';
    require_once '../classes/Permissions.php';
    require_once '../classes/Util.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();
    Dwoo\Autoloader::register();
    $dwoo = new Dwoo\Core();

    $pgdata = \ICMS\Util::getEditorPageDataStub("Dashboard", $user, "dashboard");
    $dwoo->output("tpl/dashboard.tpl", $pgdata);