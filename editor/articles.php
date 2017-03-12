<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-11
     * Time: 10:25 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../libs/dwoo/lib/Dwoo/Autoloader.php'; //Dwoo Laden
    require_once '../classes/User.php';
    require_once '../classes/Permissions.php';
    require_once '../classes/Util.php';
    require_once '../classes/Article.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();
    Dwoo\Autoloader::register();
    $dwoo = new Dwoo\Core();

    if(!isset($_GET["edit"])) {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Artikel", $user, "article");
        $dwoo->output("tpl/articles.tpl", $pgdata);
    }