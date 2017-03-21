<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 26.09.2016
     * Time: 22:17
     */

    namespace ICMS;

    class User implements \JsonSerializable {
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
        public function __construct(int $uID, string $uName, string $uRealname, string $uPassHash, string $uEmail) {
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
         * @param int $uID
         * @return User
         */
        public static function fromUID(int $uID): User {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user WHERE uID = :uid", [":uid" => $uID]);
            return new User($res->uID, $res->username, $res->realname, $res->passhash, $res->email);
        }

        /**
         * Creates a new User Object from a give username
         *
         * @param string $uName
         * @return User
         */
        public static function fromUName(string $uName): User {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user WHERE username = :uname", [":uname" => $uName]);
            return new User($res->uID, $res->username, $res->realname, $res->passhash, $res->email);
        }

        /**
         * Makes this class as an string to use for debug only
         *
         * @return string
         */
        public function __toString(): string {
            return
                "id:        ".$this->uID."\n".
                "usrname:   ".$this->uName."\n".
                "realname:  ".$this->uRealname."\n".
                "email:     ".$this->uEmail."\n";
        }

        /**
         * checks if a username is in the user db
         *
         * @param string $uName
         * @return bool
         */
        public static function doesUserNameExist(string $uName): bool {
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
        public static function getList(int $page = 1, int $pagesize = 75, string $search = "", string $sort = ""): array {
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
            $stmt = $pdo->queryPagedList("icms_user", $startElem, $endElem, ["username","realname"], $search, $USORTING[$sort], "uID >= 0");
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
        public static function getListObjects(int $page = 1, int $pagesize = 9999999, string $search = "") {
            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("icms_user", $startElem, $endElem, ["username","realname"], $search, "", "uID >= 0");
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
         * Returns the array stub for the getList() methods
         *
         * @param int $page
         * @param int $pagesize
         * @param string $search
         * @return array
         */
        public static function getListMeta(int $page, int $pagesize, string $search): array {
            $pdo = new PDO_MYSQL();
            if($search != "") $res = $pdo->query("select count(*) as size from icms_user where lower(concat(username,' ',realname)) like lower(concat('%',:search,'%')) and uID >= 0", [":search" => $search]);
            else $res = $pdo->query("select count(*) as size from icms_user where uID >= 0");
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
         * @param string $username
         * @param string $realname
         * @param string $passwdhash
         * @param string $email
         */
        public static function createUser(string $username, string $realname, string $passwdhash, string $email) {
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
         * @param string $permission
         * @return bool
         */
        public function isActionAllowed(string $permission): bool {
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
         * @param string $permission
         * @return bool
         */
        public function isActionInDB(string $permission): bool {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_user-rights WHERE uID = :uid AND `key` = :key", [":uid" => $this->uID, ":key" => $permission]);
            return isset($res->value);
        }

        /**
         * Updates a value for a specific action key or creates a new entry in the DB
         *
         * @param string $actionKey
         * @param int    $state
         */
        public function setPermission(string $actionKey, int $state) {
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
        public function getPermAsArray(): array {
            $array = [];
            $pdo = new PDO_MYSQL();
            $stmt = $pdo->queryMulti("SELECT * FROM icms_user-rights WHERE uID = :uid", [":uid" => $this->uID]);
            while($row = $stmt->fetchObject()) {
                $array[str_replace(".", "_", $row->permission)] = (int) $this->isActionAllowed($row->permission);
            }
            return $array;
        }

        /**
         * @return int
         */
        public function getUID(): int {
            return $this->uID;
        }

        /**
         * @return string
         */
        public function getUName(): string {
            return $this->uName;
        }

        /**
         * @param string $uName
         */
        public function setUName(string $uName) {
            $this->uName = $uName;
        }

        /**
         * @return string
         */
        public function getURealname(): string {
            return $this->uRealname;
        }

        /**
         * @param string $uRealname
         */
        public function setURealname(string $uRealname) {
            $this->uRealname = $uRealname;
        }

        /**
         * @return string
         */
        public function getUPassHash(): string {
            return $this->uPassHash;
        }

        /**
         * @param string $uPassHash
         */
        public function setUPassHash(string $uPassHash) {
            $this->uPassHash = $uPassHash;
        }

        /**
         * @return string
         */
        public function getUEmail(): string {
            return $this->uEmail;
        }

        /**
         * @param string $uEmail
         */
        public function setUEmail(string $uEmail) {
            $this->uEmail = $uEmail;
        }


        /**
         * @param string $passHash
         * @return bool
         */
        public function comparePassHash(string $passHash): bool {
            return $this->uPassHash == $passHash;
        }

        /**
         * Specify data which should be serialized to JSON
         *
         * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
         * @return mixed data which can be serialized by <b>json_encode</b>,
         *        which is a value of any type other than a resource.
         * @since 5.4.0
         */
        function jsonSerialize() {
            return [
                "uID" => $this->uID,
                "username" => $this->uName,
                "realname" => $this->uRealname,
                "email" => $this->uEmail
            ];
        }
    }