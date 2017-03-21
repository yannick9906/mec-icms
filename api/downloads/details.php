<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-21
     * Time: 10:53 PM
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

    $download = \ICMS\Download::fromDID(intval($_GET["id"]));
    if($download != null)
        echo json_encode($download);
    else
        echo json_encode(["error" => "ID unknown"]);