<?php
/**
 * Code is class to create codes for short url.
 * @author warma2d <warma2d@ya.ru>
 */
class Code
{

    protected $db; //PDO object
    protected $url; //full url for encode
    protected $code; //short code, example F2htm

    const CODE_LENGTH = 5;

    /**
     * @param mixed $db    
     * @param string $url
     * @return void
     */
    public function __construct($db, $url)
    {
        $this->db = $db;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function get()
    {
        $code = $this->find();

        if (isset($code['code']) AND ( strlen($code['code']) == self::CODE_LENGTH)) {
            if (($this->checkAlive($code['expires_at']))) {
                return $code['code'];
            } else {
                $this->delete($code['id']);
                $this->generateInsert();
            }
        } else {
            $this->generateInsert();
        }

        return $this->code;
    }

    /**
     * @return mixed
     */
    protected function find()
    {
        $sql = "SELECT `id`, `code`,`created_at`,`expires_at`, `url`
                FROM `short_url`
                WHERE `url` = :url ";

        $sth = $this->db->prepare($sql);
        $sth->execute(array(':url' => $this->url));
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return void
     */
    protected function generateInsert()
    {
        do {
            $this->code = $this->generate();
        } while ($this->checkExists($this->code));

        $this->insert();
    }

    /**
     * @return void
     */
    protected function insert()
    {

        $expires_at = new DateTime();
        $expires_at = $expires_at->modify('+6 months')->getTimestamp();
        $created_at = time();

        $sql = "INSERT INTO `short_url` (`code`, `url`, `expires_at`, `created_at`) 
                VALUES(:code, :url, $expires_at, $created_at)";
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':code' => $this->code, ':url' => $this->url));
    }

    /**
     * @return bool
     */
    protected function checkAlive($expires_at)
    {
        return time() < $expires_at;
    }

    /**
     * @return bool
     */
    protected function checkExists($code)
    {

        $sth = $this->db->prepare("SELECT `id` FROM short_url WHERE `code` = :code");
        $sth->execute(array(':code' => $code));
        $shortCode = $sth->fetch(PDO::FETCH_ASSOC);

        if (isset($shortCode['id']) AND $shortCode['id'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    protected function delete($id)
    {
        $sql = 'DELETE FROM `short_url` WHERE id = :id ';
        $sth = $this->db->prepare($sql);
        $sth->execute(array(':id' => $id));
    }

    /**
     * @return string
     */
    protected function generate()
    {
        $s = substr(md5($this->url . time()), 0, self::CODE_LENGTH);
        $out = '';
        for ($i = 0; $i < self::CODE_LENGTH; ++$i) {
            if (rand(0, 1) === 1) {
                $out .= strtoupper($s[$i]);
            } else {
                $out .= $s[$i];
            }
        }
        return $out;
    }
}

?>