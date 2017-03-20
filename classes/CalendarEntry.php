<?php
    /**
     * Created by PhpStorm.
     * User: yanni
     * Date: 2017-03-19
     * Time: 04:10 PM
     */

    namespace ICMS;


    class CalendarEntry implements \JsonSerializable {

        private $cID, $authorID, $name, $date, $dateUntil, $info;
        private $vID, $lastAuthorID, $lastEditDate, $version, $state;
        private $pdo;

        /**
         * CalendarEntry constructor.
         *
         * @param int    $cID
         * @param int    $authorID
         * @param string $name
         * @param string $date
         * @param        $dateUntil
         * @param string $info
         * @param int    $vID
         * @param int    $lastAuthorID
         * @param string $lastEditDate
         * @param int    $version
         * @param int    $state
         */
        public function __construct(int $cID, int $authorID, string $name, string $date, $dateUntil, string $info, int $vID, int $lastAuthorID, string $lastEditDate, int $version, int $state) {
            $this->cID = $cID;
            $this->authorID = $authorID;
            $this->name = utf8_decode($name);
            $this->date = strtotime($date);
            $this->dateUntil = $dateUntil!=null ? strtotime($dateUntil) : null;
            $this->info = utf8_decode($info);
            $this->vID = $vID;
            $this->lastAuthorID = $lastAuthorID;
            $this->lastEditDate = strtotime($lastEditDate);
            $this->version = $version;
            $this->state = $state;

            $this->pdo = new PDO_MYSQL();
        }

        public static function fromCID(int $cid): CalendarEntry {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_calendar WHERE cID = :cid ORDER BY version DESC LIMIT 1", [":cid" => $cid]);
            return new CalendarEntry($res->cID, $res->authorID, $res->name, $res->date, $res->dateUntil, $res->info, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state);
        }

        public static function fromCIDLiveOnly(int $cid): CalendarEntry {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_calendar WHERE cID = :cid and state = 0 ORDER BY version DESC LIMIT 1", [":cid" => $cid]);
            return new CalendarEntry($res->cID, $res->authorID, $res->name, $res->date, $res->dateUntil, $res->info, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state);
        }

        public static function fromVID(int $vid): CalendarEntry {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_calendar WHERE vID = :vid ORDER BY version DESC LIMIT 1", [":vid" => $vid]);
            return new CalendarEntry($res->cID, $res->authorID, $res->name, $res->date, $res->dateUntil, $res->info, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state);
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
        public static function getList(int $page = 1, int $pagesize = 75, $search = "", $sort = ""): array {
            $ASORTING = [
                "nameAsc"  => "ORDER BY name ASC",
                "idAsc"    => "ORDER BY cID ASC",
                "nameDesc" => "ORDER BY name DESC",
                "idDesc"   => "ORDER BY cID DESC",
                "dateAsc"  => "ORDER BY lastEditDate DESC",
                "dateDesc" => "ORDER BY lastEditDate DESC",
                "" => ""
            ];

            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            if($search != "") $stmt = $pdo->queryMulti("SELECT * FROM icms_calendar s1 WHERE version=(SELECT MAX(s2.version) FROM icms_calendar s2 WHERE s1.cID = s2.cID) and state >= 0 and concat(name,' ',info,' ',date) LIKE concat('%',:search,'%') ".$ASORTING[$sort]." LIMIT ".$endElem." OFFSET ".$startElem, [":search" => $search]);
            else $stmt = $pdo->queryMulti("SELECT * FROM icms_calendar s1 WHERE version=(SELECT MAX(s2.version) FROM icms_calendar s2 WHERE s1.cID = s2.cID) and state >= 0 LIMIT ".$endElem." OFFSET ".$startElem, []);
            $hits = self::getListMeta($page, $pagesize, $search);
            while($row = $stmt->fetchObject()) {
                array_push($hits["entries"], [
                    "id" => $row->cID,
                    "vId" => $row->vID,
                    "name" => $row->name,
                    "authorReal" => User::fromUID($row->authorID)->getURealname(),
                    "lastEdit" => Util::dbDateToReadableWithTime(strtotime($row->lastEditDate)),
                    "lastEditAuthor" => User::fromUID($row->lastAuthorID)->getURealname(),
                    "state" => $row->state,
                    "stateCSS" => self::stateAsCSS($row->state),
                    "stateText" => self::stateAsHtml($row->state),
                    "date" => Util::dbDateToReadableWithTime(strtotime($row->date)),
                    "dateUntil" => $row->dateUntil!=null ? Util::dbDateToReadableWithTime(strtotime($row->dateUntil)) : null,
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
         * @return Article[]
         */
        public static function getListObjects(int $page, int $pagesize, $search) {
            $pdo = new PDO_MYSQL();
            $startElem = ($page-1) * $pagesize;
            $endElem = $pagesize;
            $stmt = $pdo->queryPagedList("icms_calendar", $startElem, $endElem, ["name","date", "info"], $search);
            $hits = [];
            while($row = $stmt->fetchObject()) {
                array_push($hits, new CalendarEntry(
                    $row->cID,
                    $row->authorID,
                    $row->name,
                    $row->date,
                    $row->dateUntil,
                    $row->info,
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
        public static function getListMeta(int $page, int $pagesize, string $search): array {
            $pdo = new PDO_MYSQL();
            if($search != "") $res = $pdo->query("SELECT count(*) as size FROM (SELECT * FROM (SELECT * FROM icms_calendar WHERE concat(name,' ',info,' ',date) LIKE concat('%',:search,'%') ORDER BY cID, version desc) x GROUP BY cID) y where state >= 0", [":search" => "%".$search."%"]);
            else $res = $pdo->query("SELECT count(*) as size FROM (SELECT * FROM (SELECT * FROM icms_calendar ORDER BY cID, version desc) x GROUP BY cID) y where state >= 0");
            $size = $res->size;
            $maxpage = ceil($size / $pagesize);
            return [
                "size" => $size,
                "maxPage" => $maxpage,
                "page" => $page,
                "entries" => []
            ];
        }

        /**
         * Saves changes in fields (name, title, header, state, text) and creates a new Entry
         *
         * @param $user User
         */
        public function saveAsNewVersion(User $user) {
            $res = $this->pdo->query("SELECT MAX(version) as version FROM icms_calendar WHERE cID = :cID", [":cID" => $this->cID]);
            $authorID     = $this->authorID;
            $lastEditID   = $user->getUID();
            $lastEditDate = date("Y-m-d H:i:s");
            $cID          = $this->cID;
            $version      = $res->version + 1;
            $name         = utf8_encode($this->name);
            $info         = utf8_encode($this->info);
            $date         = date("Y-m-d H:i:s",$this->date);
            $dateUntil    = $this->dateUntil!=null ? date("Y-m-d H:i:s",$this->dateUntil) : null;
            $state        = $this->state;
            $this->pdo->queryInsert("icms_calendar",
                [
                    "cID" => $cID,
                    "authorID" => $authorID,
                    "name" => $name,
                    "info" => $info,
                    "date" => $date,
                    "dateUntil" => $dateUntil,
                    "lastAuthorID" => $lastEditID,
                    "lastEditDate" => $lastEditDate,
                    "version" => $version,
                    "state" => $state
                ]);
        }

        /**
         * @param User   $user
         * @param string $name
         * @param string $date
         * @param string $dateUntil
         * @return CalendarEntry
         */
        public static function create(User $user, string $name, string $date, string $dateUntil): CalendarEntry {
            $pdo = new PDO_MYSQL();
            $authorID = $user->getUID();
            $lastEditID = $user->getUID();
            $lastEditDate = date("Y-m-d H:i:s");
            $res = $pdo->query("SELECT MAX(cID) as cID FROM icms_calendar");
            $cID = $res->cID + 1;
            $pdo->queryInsert("icms_calendar",
                [
                    "cID" => $cID,
                    "authorID" => $authorID,
                    "name" => utf8_encode($name),
                    "date" => date("Y-m-d H:i:s", strtotime($date)),
                    "dateUntil" => date("Y-m-d H:i:s", strtotime($dateUntil)),
                    "info" => utf8_encode("> *Text hier*"),
                    "lastAuthorID" => $lastEditID,
                    "lastEditDate" => $lastEditDate,
                    "version" => 1,
                    "state" => 2
                ]);
            return self::fromCID($cID);
        }

        public function delete() {
            $this->pdo->queryUpdate("icms_calendar",
                ["state" => -1],
                "vID = :vid",[":vid" => $this->vID]);
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
                "id" => $this->cID,
                "vId" => $this->vID,
                "name" => $this->name,
                "info" => $this->info,
                "date" => Util::dbDateToReadableWithTime($this->date),
                "dateUtil" => $this->dateUntil!=null ? Util::dbDateToReadableWithTime($this->dateUntil) : null,
                "author" =>  User::fromUID($this->authorID)->getUName(),
                "authorReal" => User::fromUID($this->authorID)->getURealname(),
                "lastEdit" => Util::dbDateToReadableWithTime($this->lastEditDate),
                "lastEditAuthor" => User::fromUID($this->lastAuthorID)->getUName(),
                "state" => $this->state,
                "stateCSS" => self::stateAsCSS($this->state),
                "stateText" => self::stateAsHtml($this->state),
                "version" => $this->version,
                "Tdate" => date("Y-m-d\TH:i:s",$this->date),
                "TdateUntil" => $this->dateUntil!=null ? date("Y-m-d\TH:i:s",$this->dateUntil) : null
            ];
        }

        /**
         * turns state int into a readable production ready HTML code
         *
         * @param $state int
         * @return string
         */
        public static function stateAsHtml(int $state): string {
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
        public static function stateAsCSS(int $state): string {
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
        public function getDate(): string {
            return $this->date;
        }

        /**
         * @param string $date
         */
        public function setDate(string $date) {
            $this->date = strtotime($date);
        }

        /**
         * @return mixed
         */
        public function getDateUntil() {
            return $this->dateUntil;
        }

        /**
         * @param mixed $dateUntil
         */
        public function setDateUntil($dateUntil) {
            $this->dateUntil = $dateUntil!=null ? strtotime($dateUntil) : null;
        }

        /**
         * @return string
         */
        public function getInfo(): string {
            return $this->info;
        }

        /**
         * @param string $info
         */
        public function setInfo(string $info) {
            $this->info = $info;
        }

        /**
         * @return int
         */
        public function getVID(): int {
            return $this->vID;
        }

        /**
         * @param int $vID
         */
        public function setVID(int $vID) {
            $this->vID = $vID;
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
         * @return string
         */
        public function getLastEditDate(): string {
            return $this->lastEditDate;
        }

        /**
         * @param string $lastEditDate
         */
        public function setLastEditDate(string $lastEditDate) {
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
            return $this->cID;
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
        public function getVersion(): int {
            return $this->version;
        }
    }