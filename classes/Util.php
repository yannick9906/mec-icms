<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 26.09.2016
     * Time: 22:24
     */

    namespace ICMS;
    use DateTime;
    use DateTimeZone;

    class Util {
        /**
         * @return bool|\ICMS\User
         */
        public static function checkSession() {
            session_start();
            if(!isset($_SESSION["uID"])) {
                //echo json_encode(["success" => false, "error" => "NoLogin"]);
                self::forwardTo("login.php?err=4");
                exit;
            } else {
                $user = User::fromUID($_SESSION["uID"]);
                if($_GET["m"] == "debug") {
                    echo "<pre style='display: block; position: absolute'>\n";
                    echo "[0] Perm Array Information:\n";
                    echo "Not available on this platform";
                    echo "\n[1] Permission Information:\n";
                    echo "Not available on this platform";
                    echo "\n[2] User Information:\n";
                    echo $user->toString();
                    echo "\n[3] Client Information:\n";
                    echo "    Arguments: ".$_SERVER["REQUEST_URI"]."\n";
                    echo "    Req Time : ".$_SERVER["REQUEST_TIME"]."ns\n";
                    echo "    Remote IP: ".$_SERVER["REMOTE_ADDR"]."\n";
                    echo "    Usr Agent: ".$_SERVER["HTTP_USER_AGENT"]."\n";
                    echo "</pre>\n";
                }
                return $user;
            }
        }
        /**
         * Forwards the user to a specific url
         *
         * @param $url
         */
        public static function forwardTo($url) {
            echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\" />";
        }

        /**
         * @param string     $title
         * @param \ICMS\User $user
         * @param string     $highlight
         * @param bool       $backable
         * @param bool       $editor
         * @param string     $undoUrl
         * @return array
         */
        public static function getEditorPageDataStub($title, $user, $highlight, $backable = false, $editor = false, $undoUrl = "") {
            return [
                "header" => [
                    "title" => $title,
                    "usrname" => $user->getUName(),
                    "usrchar" => strtoupper(substr($user->getUName(), 0, 1)),
                    "realname" => $user->getURealname(),
                    "email" => $user->getUEmail(),
                    "uID" => $user->getUID(),
                    "perm" => $user->getPermAsArray(),
                    "editor" => $editor ? 1:0,
                    "undoUrl" => $undoUrl,
                    "backable" => $backable ? 1:0,
                    "highlight" => $highlight
                ],
                "perm" => $user->getPermAsArray()
            ];
        }

        /**
         * Returns the timestamp as an readable production ready text (w/ Time)
         *
         * @param $timestamp int input datetime
         * @return string
         */
        public static function dbDateToReadableWithTime($timestamp) {
            return date("d. M Y H:i", $timestamp);
        }

        /**
         * Returns the timestamp as an readable production ready text (w/o time, only date)
         *
         * @param $timestamp int input datetime
         * @return string
         */
        public static function dbDateToReadableWithOutTime($timestamp) {
            return date("d. M Y", $timestamp);
        }

        public static function redGreenNegPos($value) {
            if($value > 0) return "<span class='green-text'>+$value S</span>";
            if($value == 0) return "<span class='black-text'>$value S</span>";
            else return "<span class='red-text'>$value S</span>";
        }
    }