<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-21
     * Time: 07:22 PM
     */

    namespace ICMS;


    class Download implements \JsonSerializable {
        private $dID, $aID, $fID, $name;
        private $authorID;
        private $pdo;

        /**
         * Download constructor.
         *
         * @param int    $dID
         * @param int    $aID
         * @param int    $fID
         * @param string $name
         * @param int    $authorID
         */
        public function __construct(int $dID, int $aID, int $fID, string $name, int $authorID) {
            $this->dID = $dID;
            $this->aID = $aID;
            $this->fID = $fID;
            $this->name = utf8_decode($name);
            $this->authorID = $authorID;
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Creates a new Download Object from a give download ID
         *
         * @param int $dID
         * @return Download
         */
        public static function fromDID(int $dID): Download {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_downloads WHERE dID = :did", [":did" => $dID]);
            return new Download($res->dID, $res->aID, $res->fID, $res->name, $res->authorID);
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
                "nameAsc"  => "ORDER BY name ASC",
                "idAsc"    => "ORDER BY dID ASC",
                "nameDesc" => "ORDER BY name DESC",
                "idDesc"   => "ORDER BY dID DESC",
                "" => ""
            ];

            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("icms_downloads", $startElem, $endElem, ["name"], $search, $USORTING[$sort], "dID >= 0");
            $hits = self::getListMeta($page, $pagesize, $search);
            while($row = $stmt->fetchObject()) {
                array_push($hits["downloads"], [
                    "id" => $row->dID,
                    "name" => utf8_decode($row->name),
                    "file" => File::fromFID($row->fID),
                    "article" => Article::fromAID($row->aID)->minArray(),
                    "author" => User::fromUID($row->authorID)->getURealname(),
                    "check" => md5($row->dID+$row->fID+$row->aID+$row->authorID+$row->name)
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
            $stmt = $pdo->queryPagedList("icms_downlaods", $startElem, $endElem, ["name"], $search, "", "dID >= 0");
            $hits = [];
            while($row = $stmt->fetchObject()) {
                array_push($hits, new Download(
                        $row->dID,
                        $row->aID,
                        $row->fID,
                        $row->name,
                        $row->authorID)
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
            if($search != "") $res = $pdo->query("select count(*) as size from icms_downloads where lower(concat(fileName,' ',dateUploaded)) like lower(concat('%',:search,'%')) and fID >= 0", [":search" => $search]);
            else $res = $pdo->query("select count(*) as size from icms_downloads where fID >= 0");
            $size = $res->size;
            $maxpage = ceil($size / $pagesize);
            return [
                "size" => $size,
                "maxPage" => $maxpage,
                "page" => $page,
                "downloads" => []
            ];
        }

        /**
         * Deletes a download
         *
         * @return bool
         */
        public function delete() {
            $stmt = $this->pdo->queryMulti("DELETE FROM icms_downloads WHERE dID = :did", [":did" => $this->dID]);
            return $stmt->errorCode() == 0;
        }

        /**
         * Saves the Changes made to this object to the db
         */
        public function saveChanges() {
            $this->pdo->queryUpdate("icms_downloads",
                ["aID" => $this->aID,
                 "fID" => $this->fID,
                 "authorID" => $this->authorID,
                 "name" => utf8_decode($this->name)],
                "dID = :did",
                ["did" => $this->dID]
            );
        }

        /**
         * Creates a new download from the give attribs
         *
         * @param string $name
         * @param int    $fID
         * @param int    $aID
         * @param User   $user
         */
        public static function createDownload(string $name, int $fID, int $aID, User $user) {
            $pdo = new PDO_MYSQL();
            $pdo->queryInsert("icms_downloads",
                ["fID" => $fID,
                 "aID" => $aID,
                 "authorID" => $user->getUID(),
                 "name" => utf8_decode($name)]
            );
        }

        /**
         * @return int
         */
        public function getDID(): int {
            return $this->dID;
        }

        /**
         * @return int
         */
        public function getAuthorID(): int {
            return $this->authorID;
        }

        /**
         * @return int
         */
        public function getAID(): int {
            return $this->aID;
        }

        /**
         * @param int $aID
         */
        public function setAID(int $aID) {
            $this->aID = $aID;
        }

        /**
         * @return int
         */
        public function getFID(): int {
            return $this->fID;
        }

        /**
         * @param int $fID
         */
        public function setFID(int $fID) {
            $this->fID = $fID;
        }

        /**
         * @return string
         */
        public function getName(): string {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName(string $name) {
            $this->name = $name;
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
                "id" => $this->dID,
                "aID" => $this->aID,
                "fID" => $this->fID,
                "authorID" => $this->authorID,
                "name" => $this->name
            ];
        }
    }