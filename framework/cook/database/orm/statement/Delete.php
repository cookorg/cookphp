<?php

namespace cook\database\orm\statement;

/**
 * 删除类
 */
class Delete extends Statement {

    /**
     * @return string
     */
    public function getSql() {
        if (empty($this->table)) {
            trigger_error('No table is set for deletion', E_USER_ERROR);
        }
        $sql = 'DELETE FROM ' . $this->table;
        $sql .= $this->Where;
        $sql .= $this->Order;
        $sql .= $this->Limit;

        return $sql;
    }

    /**
     * @return \PDOstatement
     */
    public function execute() {
        return $this->db->exec($this->getSql(), $this->values)->rowCount();
    }

}
