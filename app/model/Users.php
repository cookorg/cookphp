<?php

namespace app\model;

use cook\core\Model;

class Users extends Model {

    //账号不存在
    public $accountnot = -1;
    //已禁用
    public $disabled = -2;
    //密码不正确
    public $incorrectpassword = -3;

    /**
     * 登录
     * @param string $username
     * @param string $password
     * @return int
     */
    public function login($username, $password) {
        $query = $this->db->ORM->select($this->form)->columns('uid,status,password')->where('username', 'eq', $username)->limit(1)->execute()->fetch();
        if (!empty($query['uid'])) {
            if (intval($query['status']) === 1) {
                return sha1($password) === $query['password'] ? intval($query['uid']) : $this->incorrectpassword;
            }
            return $this->disabled;
        }
        return $this->accountnot;
    }

}
