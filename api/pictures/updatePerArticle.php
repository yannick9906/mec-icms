<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-23
     * Time: 01:06 AM
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

    $article = \ICMS\Article::fromAID(intval($_POST["id"]));
    if($article->getAID() != null) {
        $article->setPictures($_POST["fIDs"]);
        echo json_encode(["success" => true]);
    } else echo json_encode(["success" => false, "error" => "ID unknown"]);