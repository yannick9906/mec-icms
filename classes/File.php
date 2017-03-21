<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 04:09 PM
     */

    namespace ICMS;


    class File implements \JsonSerializable {
        private $fID, $fileName, $filePath;
        private $authorID, $dateUploaded;
        private $pdo;

        /**
         * File constructor.
         *
         * @param int    $fID
         * @param string $fileName
         * @param string $filePath
         * @param int    $authorID
         * @param string $dateUploaded
         */
        public function __construct(int $fID, string $fileName, string $filePath, int $authorID, string $dateUploaded) {
            $this->fID = $fID;
            $this->fileName = utf8_decode($fileName);
            $this->filePath = utf8_decode($filePath);
            $this->authorID = $authorID;
            $this->dateUploaded = strtotime($dateUploaded);
            $this->pdo = new PDO_MYSQL();
        }

        /**
         * Creates a new File Object from a give file ID
         *
         * @param int $fID
         * @return File
         */
        public static function fromFID(int $fID): File {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_files WHERE fID = :fid", [":fid" => $fID]);
            return new File($res->fID, $res->fileName, $res->filePath, $res->authorID, $res->dateUploaded);
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
                "nameAsc"  => "ORDER BY fileName ASC",
                "idAsc"    => "ORDER BY fID ASC",
                "nameDesc" => "ORDER BY fileName DESC",
                "idDesc"   => "ORDER BY fID DESC",
                "" => ""
            ];

            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("icms_files", $startElem, $endElem, ["fileName","dateUploaded"], $search, $USORTING[$sort], "fID >= 0");
            $hits = self::getListMeta($page, $pagesize, $search);
            while($row = $stmt->fetchObject()) {
                $filePath = utf8_decode($row->filePath);
                array_push($hits["files"], [
                    "id" => $row->fID,
                    "fileName" => utf8_decode($row->fileName),
                    "filePath" => (strlen($filePath) > 56) ? substr($filePath,0,53).'...' : $filePath,
                    "uploaded" => Util::dbDateToReadableWithTime(strtotime($row->dateUploaded)),
                    "author" => User::fromUID($row->authorID)->getURealname(),
                    "check" => md5($row->fID+$row->fileName+$row->filePath+$row->dateUploaded)
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
            $stmt = $pdo->queryPagedList("icms_files", $startElem, $endElem, ["fileName","dateUploaded"], $search, "", "fID >= 0");
            $hits = [];
            while($row = $stmt->fetchObject()) {
                array_push($hits, new File(
                        $row->fID,
                        $row->filename,
                        $row->filePath,
                        $row->authorID,
                        $row->dateUploaded)
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
            if($search != "") $res = $pdo->query("select count(*) as size from icms_files where lower(concat(fileName,' ',dateUploaded)) like lower(concat('%',:search,'%')) and fID >= 0", [":search" => $search]);
            else $res = $pdo->query("select count(*) as size from icms_files where fID >= 0");
            $size = $res->size;
            $maxpage = ceil($size / $pagesize);
            return [
                "size" => $size,
                "maxPage" => $maxpage,
                "page" => $page,
                "files" => []
            ];
        }

        /**
         * Deletes a user
         *
         * @return bool
         */
        public function delete() {
            if(rename("/var/www/html/icms/".$this->filePath, "/var/www/html/icms/".str_replace("uploads/","uploads/__",$this->filePath))) {
                $this->pdo->query("DELETE FROM icms_files WHERE fID = :fid", [":fid" => $this->fID]);
                return true;
            } else return false;
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
         * Creates a new file from the give attribs
         *
         * @param string $name
         * @param        $file
         * @param User   $user
         * @return int
         */
        public static function createFile(string $name, $file, User $user): int {
            $pdo = new PDO_MYSQL();
            $row = $pdo->query("select max(fID) as idMax from icms_files;");
            $fileID = $row->idMax + 1;

            $pdo->queryInsert("icms_files",
                ["fileName" => utf8_decode($name),
                 "filePath" => "uploads/".$fileID."_".str_replace("#","%23",basename($file["name"])),
                 "authorID" => $user->getUID(),
                 "dateUploaded" => date("Y-m-d H:i:s")]
            );
            return $fileID;
        }

        /**
         * @return string
         */
        public function getFileName(): string {
            return $this->fileName;
        }

        /**
         * @param string $fileName
         */
        public function setFileName(string $fileName) {
            $this->fileName = $fileName;
        }

        /**
         * @return string
         */
        public function getFilePath(): string {
            return $this->filePath;
        }

        /**
         * @param string $filePath
         */
        public function setFilePath(string $filePath) {
            $this->filePath = $filePath;
        }

        /**
         * @return int
         */
        public function getFID(): int {
            return $this->fID;
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
        public function getDateUploaded(): int {
            return $this->dateUploaded;
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
                "id" => $this->fID,
                "name" => $this->fileName,
                "path" => $this->filePath,
                "authorReal" => User::fromUID($this->authorID)->getURealname()
            ];
        }
    }