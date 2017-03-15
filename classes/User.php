<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 26.09.2016
     * Time: 22:17
     */

    namespace ICMS;

    /*const USORTING = [
        "ascName"  => " ORDER BY username ASC",
        "ascID"    => " ORDER BY uID ASC",
        "descName" => " ORDER BY username DESC",
        "descID"   => " ORDER BY uID DESC",
        "" => ""
    ];*/

    class User {
        private $pdo, $uID, $uName, $uRealname, $uPassHash, $uEmail;

        /**
         * User constructor.
         *
         * @param int    $uID
         * @param string $uName
         * @param string $uRealname
         * @param string $uPassHash
         * @param string $uEmail
         */
        public function __construct($uID, $uName, $uRealname, $uPassHash, $uEmail) {
            $this->uID = $uID;
            $this->uName = utf8_encode($uName);
            $this->uRealname = utf8_encode($uRealname);
            $this->uPassHash = $uPassHash;
            $this->uEmail = $uEmail;
            $this->pdo = new PDO_MYSQL();
        }
        /**
         * Creates a new User Object from a give user ID
         *
         * @param $uID int User ID
         * @return User
         */
        public static function fromUID($uID) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user WHERE uID = :uid", [":uid" => $uID]);
            return new User($res->uID, $res->username, $res->realname, $res->passhash, $res->email);
        }

        /**
         * Creates a new User Object from a give username
         *
         * @param $uName string Username
         * @return User
         */
        public static function fromUName($uName) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user WHERE username = :uname", [":uname" => $uName]);
            return new User($res->uID, $res->username, $res->realname, $res->passhash, $res->email);
        }
        /**
         * Makes this class as an array to use for tables etc.
         *
         * @return array
         */
        public function asArray() {
            return [
                "uID" => $this->uID,
                "username" => $this->uName,
                "realname" => $this->uRealname,
                "email" => $this->uEmail
            ];
        }
        /**
         * Makes this class as an string to use for debug only
         *
         * @return string
         */
        public function toString() {
            return
                "id:        ".$this->uID."\n".
                "usrname:   ".$this->uName."\n".
                "realname:  ".$this->uRealname."\n".
                "email:     ".$this->uEmail."\n";
        }
        /**
         * checks if a username is in the user db
         *
         * @param $uName string Username
         * @return bool
         */
        public static function doesUserNameExist($uName) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user WHERE username = :uname", [":uname" => $uName]);
            return isset($res->uID);
        }

        /**
         * Returns all entries matching the search and the page
         *
         * @param int    $page
         * @param int    $pagesize
         * @param string $search
         * @param string $sort
         *
         * @return array Normal dict array with data
         */
        public static function getList($page = 1, $pagesize = 75, $search = "", $sort = "") {
            $USORTING = [
                "nameAsc"  => "ORDER BY username ASC",
                "idAsc"    => "ORDER BY uID ASC",
                "nameDesc" => "ORDER BY username DESC",
                "idDesc"   => "ORDER BY uID DESC",
                "" => ""
            ];

            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("icms_user", $startElem, $endElem, ["username","realname"], $search, $USORTING[$sort]);
            $hits = self::getListMeta($page, $pagesize, $search);
            while($row = $stmt->fetchObject()) {
                array_push($hits["users"], [
                    "uID" => $row->uID,
                    "username" => utf8_encode($row->username),
                    "realname" => utf8_encode($row->realname),
                    "email" => utf8_encode($row->email),
                    "check" => md5($row->uID+$row->username+$row->realname+$row->email)
                ]);
            }
            return $hits;
        }
        /**
         * @see getList()
         * but you'll get Objects instead of an array
         *
         * @param int $page
         * @param int $pagesize
         * @param string $search
         *
         * @return User[]
         */
        public static function getListObjects($page, $pagesize, $search) {
            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("icms_user", $startElem, $endElem, ["username","realname"], $search);
            $hits = [];
            while($row = $stmt->fetchObject()) {
                array_push($hits, new User(
                        $row->uID,
                        $row->username,
                        $row->realname,
                        $row->passHash,
                        $row->email)
                );
            }
            return $hits;
        }
        /**
         * Returns the array stub for the getLists() method
         *
         * @param int $page
         * @param int $pagesize
         * @param string $search
         * @return array
         */
        public static function getListMeta($page, $pagesize, $search) {
            $pdo = new PDO_MYSQL();
            if($search != "") $res = $pdo->query("select count(*) as size from icms_user where lower(concat(username,' ',realname)) like lower(:search)", [":search" => "%".$search."%"]);
            else $res = $pdo->query("select count(*) as size from icms_user");
            $size = $res->size;
            $maxpage = ceil($size / $pagesize);
            return [
                "size" => $size,
                "maxPage" => $maxpage,
                "page" => $page,
                "users" => []
            ];
        }
        /**
         * Deletes a user
         *
         * @return bool
         */
        public function delete() {
            return $this->pdo->query("DELETE FROM icms_user WHERE uID = :uid", [":uid" => $this->uID]);
        }
        /**
         * Saves the Changes made to this object to the db
         */
        public function saveChanges() {
            $this->pdo->queryUpdate("icms_user",
                ["username" => utf8_decode($this->uName),
                 "realname" => utf8_decode($this->uRealname),
                 "passhash" => $this->uPassHash,
                 "email" => utf8_decode($this->uEmail)],
                "uID = :uid",
                ["uid" => $this->uID]
            );
        }

        /**
         * Creates a new user from the give attribs
         *
         * @param $username   string Username
         * @param $realname   string Realname of the user
         * @param $passwdhash string md5 Hash of Password
         * @param $email
         */
        public static function createUser($username, $realname, $passwdhash, $email) {
            $pdo = new PDO_MYSQL();
            $pdo->queryInsert("icms_user",
                ["username" => utf8_decode($username),
                 "realname" => utf8_decode($realname),
                 "passhash" => $passwdhash,
                 "email" => utf8_decode($email)]
            );
        }

        /**
         * Checks if the user is permitted to do sth.
         *
         * @param $permission String for Permission
         * @return bool
         */
        public function isActionAllowed($permission) {
            if($this->uPrefix != 27) {
                $pdo = new PDO_MYSQL();
                $res = $pdo->query("SELECT * FROM icms_user-rights WHERE uID = :uid AND `key` = :key", [":uid" => $this->uID, ":key" => $permission]);
                if($res->value == 1) return true;
                else return false;
            } else return true;
        }

        /**
         * Tests if a permission action key is already present in the DB
         *
         * @param $permission string
         * @return bool
         */
        public function isActionInDB($permission) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user-rights WHERE uID = :uid AND `key` = :key", [":uid" => $this->uID, ":key" => $permission]);
            return isset($res->value);
        }

        /**
         * Updates a value for a specific action key or creates a new entry in the DB
         *
         * @param $actionKey string
         * @param $state int
         */
        public function setPermission($actionKey, $state) {
            $pdo = new PDO_MYSQL();
            if($this->isActionInDB($actionKey))
                $pdo->query("UPDATE icms_user-rights SET `value` = :state WHERE uID = :uid and `key` = :key", [":uid" => $this->uID, ":key" => $actionKey, ":state" => $state]);
            else
                $pdo->query("INSERT INTO icms_user-rights(`value`, uID, `key`) VALUES (:state, :uid, :key)", [":uid" => $this->uID, ":key" => $actionKey, ":state" => $state]);
        }

        /**
         * Returns all permission for this user as an use-ready array
         *
         * @return array
         */
        public function getPermAsArray() {
            $array = [];
            $pdo = new PDO_MYSQL();
            $stmt = $pdo->queryMulti("SELECT * FROM entrance_user_rights WHERE uID = :uid", [":uid" => $this->uID]);
            while($row = $stmt->fetchObject()) {
                $array[str_replace(".", "_", $row->permission)] = (int) $this->isActionAllowed($row->permission);
            }
            return $array;
        }

        /**
         * @return int
         */
        public function getUID() {
            return $this->uID;
        }
        /**
         * @param int $uID
         */
        public function setUID($uID) {
            $this->uID = $uID;
        }
        /**
         * @return string
         */
        public function getUName() {
            return $this->uName;
        }
        /**
         * @param string $uName
         */
        public function setUName($uName) {
            $this->uName = $uName;
        }
        /**
         * @return string
         */
        public function getURealname() {
            return $this->uRealname;
        }
        /**
         * @param string $uRealname
         */
        public function setURealname($uRealname) {
            $this->uRealname = $uRealname;
        }
        /**
         * @return string
         */
        public function getUPassHash() {
            return $this->uPassHash;
        }
        /**
         * @param string $uPassHash
         */
        public function setUPassHash($uPassHash) {
            $this->uPassHash = $uPassHash;
        }
        public function comparePassHash($passHash) {
            return $this->uPassHash == $passHash;
        }

        /**
         * @return string
         */
        public function getUEmail() {
            return $this->uEmail;
        }

        /**
         * @param string $uEmail
         */
        public function setUEmail($uEmail) {
            $this->uEmail = $uEmail;
        }
    }