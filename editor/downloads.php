<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-21
     * Time: 07:23 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../vendor/autoload.php';
    require_once '../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../classes/User.php';
    require_once '../classes/Permissions.php';
    require_once '../classes/Util.php';
    require_once '../classes/Download.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();
    $dwoo = new Dwoo\Core();

    if(!isset($_GET["edit"])) {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Downloads", $user, "downloads");
        echo $dwoo->get("tpl/downloads.tpl", $pgdata);
    } else {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Artikel bearbeiten", $user, "article");
        $article = \ICMS\Article::fromVID(intval($_GET["edit"]));
        $pgdata['article'] = $article->jsonSerialize();
        echo $dwoo->get("tpl/down.tpl", $pgdata);
    }