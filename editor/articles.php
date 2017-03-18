<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-11
     * Time: 10:25 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../vendor/autoload.php';
    require_once '../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../classes/User.php';
    require_once '../classes/Permissions.php';
    require_once '../classes/Util.php';
    require_once '../classes/Article.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();
    $dwoo = new Dwoo\Core();

    if(!isset($_GET["edit"])) {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Artikel", $user, "article");
        echo $dwoo->get("tpl/articles.tpl", $pgdata);
    } else {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Artikel bearbeiten", $user, "article");
        $article = \ICMS\Article::fromAID(intval($_GET["edit"]));
        $pgdata['article'] = $article->jsonSerialize();
        echo $dwoo->get("tpl/articleEdit.tpl", $pgdata);
    }