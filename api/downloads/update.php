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

    $download = \ICMS\Download::fromDID(intval($_POST["id"]));
    $name = $_POST["name"];
    $fID = $_POST["fID"];
    $aID = $_POST["aID"];

    if($name != "" && $fID != "" && $aID) {
        $download->setName($name);
        $download->setAID($aID);
        $download->setFID($fID);
        $download->saveChanges();
        echo json_encode(["success" => "1"]);
    } else echo json_encode(["success" => "0", "error" => "missing fields"]);