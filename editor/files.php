<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 03:57 PM
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

    $pgdata = \ICMS\Util::getEditorPageDataStub("Dateien", $user, "files");
    echo $dwoo->get("tpl/files.tpl", $pgdata);
