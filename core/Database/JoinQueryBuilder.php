<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait JoinQueryBuilder
{
    
    /**
     * make query for join
     * @param mixed $relations 
     * @param string $primary_key 
     * @param mixed|null $foreign_key 
     * @param mixed|null $mode 
     * @return $this 
     */
    public function join($relations, $primary_key = "id", $foreign_key = null, $mode = null)
    {
        $extract = explode(":", $relations);
        $table   = $extract[0];
        $alias   = $extract[1] ?? $table;

        $foreign_key = $foreign_key ?? $this->table . "_" . $primary_key;

        $this->join .= "
            $mode JOIN $table " . ($alias != $table ? $alias : "") . " ON $this->table.$primary_key = $alias.$foreign_key
        ";
        $this->relationSelect = array_merge($this->relationSelect, $this->getRelationColumns($table, $alias, $foreign_key));
        
        return $this;
    }

    /**
     * get current columns
     * @return array 
     */
    protected function getCurrentColumns()
    {
        $query   = "SHOW COLUMNS FROM $this->table";
        $prepare = $this->db->query($query);
        $res     = $prepare->result();

        // filter
        $hidden  = $this->hidden;
        $columns = array_filter(array_column($res, "Field"), function($key, $val) use ($hidden) {
            return !in_array($key, $hidden);
        }, ARRAY_FILTER_USE_BOTH);

        // mapping
        $table = $this->table;
        return array_map(function($val) use ($table) {
            return "$table.$val";
        }, $columns);
    }

    /**
     * get relation columns
     * @param mixed $table 
     * @param mixed $alias 
     * @param mixed $foreign_key 
     * @return array 
     */
    public function getRelationColumns($table, $alias, $foreign_key)
    {
        $query   = "SHOW COLUMNS FROM $table";
        $prepare = $this->db->query($query);
        $res     = $prepare->result();

        // filter
        $columns = array_filter(array_column($res, "Field"), function($key, $val) use ($foreign_key) {
            return $key != $foreign_key;
        }, ARRAY_FILTER_USE_BOTH);

        return array_map(function($val) use ($alias) {
            return "$alias.$val as " . $alias . "_" . $val;
        }, $columns);
    }

}
