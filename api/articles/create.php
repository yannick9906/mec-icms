<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-18
     * Time: 10:15 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../../classes/User.php';
    require_once '../../classes/Permissions.php';
    require_once '../../classes/Util.php';
    require_once '../../classes/Article.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();

    $name = $_POST["name"];
    $title = $_POST["title"];

    \ICMS\Article::create($user, $name, $title);

    echo json_encode(["success" => true]);