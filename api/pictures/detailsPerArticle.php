<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-22
     * Time: 11:51 PM
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

    $article = \ICMS\Article::fromAID(intval($_GET["id"]));
    if($article->getAID() != null)
        echo json_encode($article->getPictureDetails());
    else
        echo json_encode(["error" => "ID unknown"]);