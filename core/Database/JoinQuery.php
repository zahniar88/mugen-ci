<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait JoinQuery
{
    
    /**
     * joining table
     * @param mixed $table 
     * @param string $primary_key 
     * @param mixed|null $foreign_key 
     * @param string $mode 
     * @return $this 
     */
    public function join($table, $primary_key = "id", $foreign_key = null, $mode = "")
    {
        $foreign_key = isset($foreign_key) ? $foreign_key : $this->table . "_" . $primary_key;

        // modify columns table
        $table_parent = $this->table;
        $parent_cols = array_map(function($cols) use ($table_parent) {
            return "$table_parent.$cols";
        }, $this->cols);

        // merge
        $this->cols = array_merge($this->cols, $parent_cols, $this->getColumns($table));

        $this->join .= "$mode JOIN $table ON $this->table.$primary_key = $table.$foreign_key";

        return $this;
    }

    /**
     * get columns name
     * @param mixed $table 
     * @return array 
     */
    protected function getColumns($table)
    {
        $query = "SHOW COLUMNS FROM $table";
        $prepare = $this->db->query($query);

        $columns = array_map(function ($cols) use ($table) {
            return "$table.$cols as $table" . "_" . "$cols";
        }, array_column($prepare->result_array(), "Field"));

        return $columns;
    }

}
