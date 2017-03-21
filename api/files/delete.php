<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 04:08 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../../classes/User.php';
    require_once '../../classes/Permissions.php';
    require_once '../../classes/Util.php';
    require_once '../../classes/File.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();

    $fID = intval($_GET["fID"]);

    $file = \ICMS\File::fromFID($fID);
    echo $file->delete() ? json_encode(["success" => true]) : json_encode(["success" => false]);
    //TODO checks if used anywhere else