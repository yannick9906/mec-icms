<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 04:10 PM
     */
    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../../classes/User.php';
    require_once '../../classes/Permissions.php';
    require_once '../../classes/Util.php';
    require_once '../../classes/CalendarEntry.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();

    $cID = intval($_POST["cID"]);
    $name = $_POST["name"];
    $info = $_POST["info"];
    $date = $_POST["date"];
    $dateUntil = $_POST["dateUntil"];
    $state = intval($_POST["state"]);

    $entry = \ICMS\CalendarEntry::fromCID($cID);

    if($entry->getAuthorID() != null) {
        $entry->setName($name);
        $entry->setInfo($info);
        $entry->setState($state);
        $entry->setDate($date);
        $entry->setDateUntil($dateUntil);
        $entry->saveAsNewVersion($user);

        echo json_encode(["success" => true]);
    } else echo json_encode(["success" => false, "error" => "idNotFound"]);
