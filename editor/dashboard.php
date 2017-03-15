<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 28.09.2016
     * Time: 21:48
     */
    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../vendor/autoload.php';
    require_once '../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../classes/User.php';
    require_once '../classes/Permissions.php';
    require_once '../classes/Util.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();
    $dwoo = new Dwoo\Core();

    $pgdata = \ICMS\Util::getEditorPageDataStub("Dashboard", $user, "dashboard");
    echo $dwoo->get("tpl/dashboard.tpl", $pgdata);