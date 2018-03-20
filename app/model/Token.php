<?php

namespace app\model;

use cook\core\Model;

/**
 * Description of Token
 *
 * @author xuai
 */
class Token extends Model {

    /**
     * 返回用户Token
     * @param int $uid
     * @return false|string
     */
    public function getToken($uid) {
        $token = sha1($uid . microtime());
        if ($this->db->ORM->insert(['uid' => intval($uid), 'token' => $token], $this->form)->replace(true)->execute()) {
            return $token;
        }
        return false;
    }

}
