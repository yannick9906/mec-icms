<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 04.10.2016
     * Time: 22:36
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../../classes/User.php';
    require_once '../../classes/Permissions.php';
    require_once '../../classes/Util.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();

    $userToEdit = \ICMS\User::fromUID(intval($_GET["id"]));
    $realname = $_POST["realname"];
    $passhash = $_POST["passhash"];
    $email = $_POST["email"];

    if($realname != "" && $passhash != "" && $email) {
        $userToEdit->setUEmail($email);
        if($passhash != "NOUPDATE") $userToEdit->setUPassHash($passhash);
        $userToEdit->setURealname($realname);
        $userToEdit->saveChanges();
        echo json_encode(["success" => "1"]);
    } else echo json_encode(["success" => "0", "error" => "missing fields"]);