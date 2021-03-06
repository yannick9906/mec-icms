<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 01:30 AM
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

    $aID = intval($_GET["aID"]);

    $article = \ICMS\Article::fromAID($aID);
    $article->delete();
    //TODO checks if used anywhere else
    echo json_encode(["success" => true]);