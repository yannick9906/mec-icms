<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-12
     * Time: 01:29 AM
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

    $users = \ICMS\Article::getList($_GET["page"], intval($_GET["pagesize"]), $_GET["search"], $_GET["sort"]);
    echo json_encode($users);
