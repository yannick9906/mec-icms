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
         * @param date   $lastEditDate
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
            $this->lastEditDate = $lastEditDate;
            $this->version = $version;
            $this->state = $state;

            $this->pdo = new PDO_MYSQL();
        }

        public static function fromAID($aid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_articles WHERE aID = :aid ORDER BY version DESC LIMIT 1", [":aid" => $aid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state)
        }

        public static function fromAIDLiveOnly($aid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_articles WHERE aID = :aid and state = 0 ORDER BY version DESC LIMIT 1", [":aid" => $aid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state)
        }

        public static function fromVID($vid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM icms_articles WHERE vID = :vid ORDER BY version DESC LIMIT 1", [":vid" => $vid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state)
        }

        public static function fromVID($vid) {
            $pdo = new PDO_MYSQL();
            $res = $pdo->query("SELECT * FROM schlopolis_sites WHERE ID = :vid ORDER BY version DESC LIMIT 1", [":vid" => $vid]);
            return new Article($res->aID, $res->authorID, $res->name, $res->title, $res->header, $res->text, $res->vID, $res->lastAuthorID, $res->lastEditDate, $res->version, $res->state)
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
                    return "check";
                    break;
                case 1:
                    return "account-alert";
                    break;
                case 2:
                    return "close";
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
                    return "green-text";
                    break;
                case 1:
                    return "orange-text";
                    break;
                case 2:
                    return "red-text";
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
         * @return date
         */
        public function getLastEditDate(): date {
            return $this->lastEditDate;
        }

        /**
         * @param date $lastEditDate
         */
        public function setLastEditDate(date $lastEditDate) {
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