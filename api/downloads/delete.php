<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-21
     * Time: 07:22 PM
     */
    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../../classes/User.php';
    require_once '../../classes/Permissions.php';
    require_once '../../classes/Util.php';
    require_once '../../classes/Download.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();

    $dID = intval($_GET["dID"]);

    $download = \ICMS\Download::fromDID($dID);
    echo $download->delete() ? json_encode(["success" => true]) : json_encode(["success" => false]);
