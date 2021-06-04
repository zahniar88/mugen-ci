<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait JoinQueryBuilder
{
    /**
     * join columns
     * @var array
     */
    protected $joinColumns = [];

    /**
     * make join query
     * @param string $relation 
     * @param string $primary_key 
     * @param string|null $foreign_key 
     * @param string|null $mode 
     * @return $this 
     */
    public function join($relation, $primary_key = "id", $foreign_key = null, $mode = null)
    {
        $extract = explode(":", $relation);
        $table   = $extract[0];
        $alias   = $extract[1] ?? $table;

        $foreign_key = isset($foreign_key) ? $foreign_key : $this->table . "_" . $primary_key;
        $this->join .= "$mode JOIN $table " . ($alias != $table ? "AS " .$alias : "") . " ON #current_table#.$primary_key = $alias.$foreign_key\n";
        $this->joinColumns = array_merge($this->joinColumns, $this->getJoinColumns($table, $alias, $foreign_key));

        return $this;
    }

    /**
     * get column join
     * @param string $table 
     * @param string $alias 
     * @param string $foreign_key 
     * @return array 
     */
    protected function getJoinColumns($table, $alias, $foreign_key)
    {
        $driver   = $this->db->subdriver ?? $this->db->dbdriver;
        $database = $this->db->database;

        $query   = $this->getQueryColumns($table, $database, $driver);
        $prepare = $this->db->query($query);
        $columns = array_column($prepare->result(), "column_name");

        $columns = array_filter($columns, function($key) use ($foreign_key) {
            return $key !== $foreign_key;
        }, ARRAY_FILTER_USE_BOTH);

        return array_map(function($val) use ($alias) {
            return "$alias.$val as " . $alias . "_" . $val;
        }, $columns);
    }
    
    /**
     * get column current table
     * @return array 
     */
    protected function getCurrentColumns()
    {
        $driver   = $this->db->subdriver ?? $this->db->dbdriver;
        $database = $this->db->database;

        $query   = $this->getQueryColumns($this->table, $database, $driver);
        $prepare = $this->db->query($query);
        $columns = array_column($prepare->result(), "column_name");

        $hidden  = $this->hidden;
        $columns = array_filter($columns, function($key) use ($hidden) {
            return !in_array($key, $hidden);
        }, ARRAY_FILTER_USE_BOTH);

        $table = $this->alias ? $this->alias : $this->table;
        return array_map(function($val) use ($table) {
            return "$table.$val";
        }, $columns);
    }

    /**
     * generate query for get column
     * @param string $table 
     * @param string $database 
     * @param string $driver 
     * @return string 
     */
    protected function getQueryColumns($table, $database, $driver)
    {
        $query = "";
        if ( $driver == "mysqli" || $driver == "mysql" ) {
            $query = "SELECT column_name FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table'";
        }

        elseif ( $driver == "pgsql" || $driver == "postgre" ) {
            $query = "SELECT column_name FROM information_schema.\"columns\" WHERE TABLE_NAME='$table'";
        }

        return $query;
    }
    
}
