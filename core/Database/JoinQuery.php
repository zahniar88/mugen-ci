<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait JoinQuery
{

    protected $childJoinCols = [];
    protected $parentJoinCols = [];
    
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

        $this->childJoinCols = array_merge($this->childJoinCols, $this->getChildColumns($table));
        $this->join .= "$mode JOIN $table ON $this->table.$primary_key = $table.$foreign_key\n";

        return $this;
    }

    /**
     * get columns parent
     * @return array 
     */
    public function getParentColumns()
    {
        $table = $this->table;
        $query = "SHOW COLUMNS FROM $table";
        $prepare = $this->db->query($query);

        $hidden = $this->hidden;
        $columns = array_filter(array_column($prepare->result_array(), "Field"), function($key) use ($hidden) {
            return !in_array($key, $hidden);
        }, ARRAY_FILTER_USE_BOTH);

        $columns = array_map(function ($cols) use ($table) {
            return "$table.$cols";
        }, $columns);

        return $columns;
    }

    /**
     * get columns name
     * @param mixed $table 
     * @return array 
     */
    protected function getChildColumns($table)
    {
        $query = "SHOW COLUMNS FROM $table";
        $prepare = $this->db->query($query);

        $columns = array_map(function ($cols) use ($table) {
            return "$table.$cols as $table" . "_" . "$cols";
        }, array_column($prepare->result_array(), "Field"));

        return $columns;
    }

}
