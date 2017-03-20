<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 08:17 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../vendor/autoload.php';
    require_once '../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../classes/User.php';
    require_once '../classes/Permissions.php';
    require_once '../classes/Util.php';
    require_once '../classes/CalendarEntry.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();
    $dwoo = new Dwoo\Core();

    if(!isset($_GET["edit"])) {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Termine", $user, "calendar");
        echo $dwoo->get("tpl/calendar.tpl", $pgdata);
    } else {
        $pgdata = \ICMS\Util::getEditorPageDataStub("Termin bearbeiten", $user, "calendar");
        $entry = \ICMS\CalendarEntry::fromVID(intval($_GET["edit"]));
        $pgdata['entry'] = $entry->jsonSerialize();
        echo $dwoo->get("tpl/calendarEdit.tpl", $pgdata);
    }