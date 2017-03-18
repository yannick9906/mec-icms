<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-18
     * Time: 09:26 PM
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

    $aID = intval($_POST["aID"]);
    $name = $_POST["name"];
    $title = $_POST["title"];
    $state = intval($_POST["state"]);
    $header = $_POST["header"];
    $text = $_POST["text"];

    $article = \ICMS\Article::fromAID($aID);
    if($article->getAuthorID() != null) {
        $article->setName($name);
        $article->setTitle($title);
        $article->setState($state);
        $article->setHeader($header);
        $article->setText($text);
        $article->saveAsNewVersion($user);

        echo json_encode(["success" => true]);
    } else echo json_encode(["success" => false, "error" => "idNotFound"]);