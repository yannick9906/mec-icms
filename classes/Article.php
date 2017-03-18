<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 26.09.2016
     * Time: 22:18
     */

    namespace ICMS;


    class Article implements \JsonSerializable {
        private $aID, $authorID, $name, $title, $header, $text;
        private $vID, $lastAuthorID, $lastEditDate, $version, $state;
        private $pdo;

        /**
         * Article constructor.
         *
         * @param int    $aID
         * @param int    $authorID
         * @param string $name
         * @param string $title
         * @param string $header
         * @param string $text
         * @param int    $vID
         * @param int    $lastAuthorID
         * @param int    $lastEditDate
         * @param int    $version
         * @param int    $state
         */
        public function __construct($aID, $authorID, $name, $title, $header, $text, $vID, $lastAuthorID, $lastEditDate, $version, $state) {
            $this->aID = $aID;
            $this->authorID = $authorID;
            $this->name = utf8_decode($name);
            $this->title = utf8_decode($title);
            $this->header = utf8_decode($header);
            $this->text = utf8_decode($text);
            $this->vID = $vID;
            $this->lastAuthorID = $lastAuthorID;
            $this->lastEditDate = strtotime($lastEditDate);
            $this->version = $version;
            $this->state = $state;

            $this->pdo = new PDO_MYSQL();
        }

        public static function fromAID($aid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_articles WHERE aID = :aid ORDER BY version DESC LIMIT 1", [":aid" => $aid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state);
        }

        public static function fromAIDLiveOnly($aid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_articles WHERE aID = :aid and state = 0 ORDER BY version DESC LIMIT 1", [":aid" => $aid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state);
        }

        public static function fromVID($vid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_articles WHERE vID = :vid ORDER BY version DESC LIMIT 1", [":vid" => $vid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state);
        }

        /**
         * turns state int into a readable production ready HTML code
         *
         * @param $state int
         * @return string
         */
        public static function stateAsHtml($state) {
            switch ($state) {
                case 0:
                    return "Live";
                    break;
                case 1:
                    return "Warte auf Bestätigung";
                    break;
                case 2:
                    return "Nicht öffentlich";
                    break;
                case 3:
                    return "Abgelehnt";
                    break;
            }
        }
        /**
         * turns state int into a CSS class name
         *
         * @param $state int
         * @return string
         */
        public static function stateAsCSS($state) {
            switch ($state) {
                case 0:
                    return "green-text mddi mddi-check";
                    break;
                case 1:
                    return "orange-text mddi mddi-account-alert";
                    break;
                case 2:
                    return "red-text mddi mddi-earth-off";
                    break;
                case 3:
                    return "red-text mddi mddi-close";
                    break;
            }
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
                "id" => $this->aID,
                "vId" => $this->vID,
                "name" => $this->name,
                "title" => $this->title,
                "header" => $this->header,
                "text" => $this->text,
                "author" =>  User::fromUID($this->authorID)->getUName(),
                "authorReal" => User::fromUID($this->authorID)->getURealname(),
                "lastEdit" => Util::dbDateToReadableWithTime($this->lastEditDate),
                "lastEditAuthor" => User::fromUID($this->lastAuthorID)->getUName(),
                "state" => $this->state,
                "stateCSS" => self::stateAsCSS($this->state),
                "stateText" => self::stateAsHtml($this->state),
                "version" => $this->version
            ];
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
            $ASORTING = [
                "nameAsc"  => "ORDER BY name ASC",
                "idAsc"    => "ORDER BY aID ASC",
                "nameDesc" => "ORDER BY name DESC",
                "idDesc"   => "ORDER BY aID DESC",
                "dateAsc"  => "ORDER BY lastEditDate DESC",
                "dateDesc" => "ORDER BY lastEditDate DESC",
                "" => ""
            ];

            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            if($search != "") $stmt = $pdo->queryMulti("SELECT * FROM (SELECT * FROM (SELECT * FROM icms_articles WHERE state >= 0 and concat(name,' ',title,' ',header) LIKE concat('%',:search,'%') ORDER BY aID, version desc) x GROUP BY aID) y ".$ASORTING[$sort]." LIMIT ".$startElem.",".$endElem, [":search" => $search]);
            else $stmt = $pdo->queryMulti("SELECT * FROM (SELECT * FROM (SELECT * FROM icms_articles WHERE state >= 0 and concat(name,' ',title,' ',header) LIKE concat('%',:search,'%') ORDER BY aID, version desc) x GROUP BY aID) y ".$ASORTING[$sort]." LIMIT ".$startElem.",".$endElem, [":search" => $search]);
            $hits = self::getListMeta($page, $pagesize, $search);
            while($row = $stmt->fetchObject()) {
                array_push($hits["articles"], [
                    "id" => $row->aID,
                    "vId" => $row->vID,
                    "name" => $row->name,
                    "authorReal" => User::fromUID($row->authorID)->getURealname(),
                    "lastEdit" => Util::dbDateToReadableWithTime(strtotime($row->lastEditDate)),
                    "lastEditAuthor" => User::fromUID($row->lastAuthorID)->getURealname(),
                    "state" => $row->state,
                    "stateCSS" => self::stateAsCSS($row->state),
                    "stateText" => self::stateAsHtml($row->state),
                    "version" => $row->version
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
            $stmt = $pdo->queryPagedList("icms_articles", $startElem, $endElem, ["name","title", "header"], $search);
            $hits = [];
            while($row = $stmt->fetchObject()) {
                array_push($hits, new Article(
                        $row->aID,
                        $row->authorID,
                        $row->name,
                        $row->title,
                        $row->header,
                        $row->text,
                        $row->vID,
                        $row->lastAuthorID,
                        $row->lastEditDate,
                        $row->version,
                        $row->state)
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
            if($search != "") $res = $pdo->query("select count(*) as size from icms_articles where lower(concat(name,' ',title,' ',header)) like lower(:search)", [":search" => "%".$search."%"]);
            else $res = $pdo->query("select count(*) as size from icms_user");
            $size = $res->size;
            $maxpage = ceil($size / $pagesize);
            return [
                "size" => $size,
                "maxPage" => $maxpage,
                "page" => $page,
                "articles" => []
            ];
        }

        /**
         * Saves changes in fields (name, title, header, state, text) and creates a new Entry
         *
         * @param $user User
         * @return bool
         */
        public function saveAsNewVersion($user) {
            $res = $this->pdo->query("SELECT MAX(version) as version FROM icms_articles WHERE aID = :aID", [":aID" => $this->aID]);
            $authorID     = $this->authorID;
            $lastEditID   = $user->getUID();
            $lastEditDate = date("Y-m-d H:i:s");
            $aID          = $this->aID;
            $version      = $res->version + 1;
            $name         = utf8_encode($this->name);
            $text         = utf8_encode($this->text);
            $header       = utf8_encode($this->header);
            $title        = utf8_encode($this->title);
            $state        = $this->state;
            $this->pdo->queryInsert("icms_articles",
                [
                   "aID" => $aID,
                   "authorID" => $authorID,
                   "name" => $name,
                   "title" => $title,
                   "header" => $header,
                   "text" => $text,
                   "lastAuthorID" => $lastEditID,
                   "lastEditDate" => $lastEditDate,
                   "version" => $version,
                   "state" => $state
                ]);
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
         * @return string
         */
        public function getTitle(): string {
            return $this->title;
        }

        /**
         * @param string $title
         */
        public function setTitle(string $title) {
            $this->title = $title;
        }

        /**
         * @return string
         */
        public function getHeader(): string {
            return $this->header;
        }

        /**
         * @param string $header
         */
        public function setHeader(string $header) {
            $this->header = $header;
        }

        /**
         * @return string
         */
        public function getText(): string {
            return $this->text;
        }

        /**
         * @param string $text
         */
        public function setText(string $text) {
            $this->text = $text;
        }

        /**
         * @return int
         */
        public function getLastAuthorID(): int {
            return $this->lastAuthorID;
        }

        /**
         * @param int $lastAuthorID
         */
        public function setLastAuthorID(int $lastAuthorID) {
            $this->lastAuthorID = $lastAuthorID;
        }

        /**
         * @return int
         */
        public function getLastEditDate(): int {
            return $this->lastEditDate;
        }

        /**
         * @param int $lastEditDate
         */
        public function setLastEditDate(int $lastEditDate) {
            $this->lastEditDate = $lastEditDate;
        }

        /**
         * @return int
         */
        public function getState(): int {
            return $this->state;
        }

        /**
         * @param int $state
         */
        public function setState(int $state) {
            $this->state = $state;
        }

        /**
         * @return int
         */
        public function getAID(): int {
            return $this->aID;
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
        public function getVID(): int {
            return $this->vID;
        }

        /**
         * @return int
         */
        public function getVersion(): int {
            return $this->version;
        }
    }