<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 04:07 PM
     */

    ini_set("display_errors", "on");
    error_reporting(E_ALL & ~E_NOTICE);

    require_once '../../classes/PDO_Mysql.php'; //DB Anbindung
    require_once '../../classes/User.php';
    require_once '../../classes/Permissions.php';
    require_once '../../classes/Util.php';
    require_once '../../classes/File.php';

    $user = \ICMS\Util::checkSession();
    $pdo = new \ICMS\PDO_MYSQL();

    $name = $_POST["name"];

    if(isset($_FILES[0])) {
        $uploadDir = "/var/www/html/icms/uploads/";
        $file = $_FILES[0];

        $fileID = \ICMS\File::createFile($name, $file, $user);

        if(move_uploaded_file($file['tmp_name'], $uploadDir.$fileID."_".basename($file['name']))) {
            echo json_encode(["success" => true]);
        } else echo json_encode(["success" => false, "error" => "Couldn't copy file"]);
    } else echo json_encode(["success" => false, "error" => "No File submitted"]);
