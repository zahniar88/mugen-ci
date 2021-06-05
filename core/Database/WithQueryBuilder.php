<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait WithQueryBuilder
{
    /**
     * saving with data
     * @var array
     */
    protected $with = [];
    
    /**
     * merge data table
     * @param stirng $table 
     * @param string $mode 
     * @param string $primary_key 
     * @param string|null $foreign_key 
     * @return $this 
     */
    public function with($table, $mode = "", $primary_key = "id", $foreign_key = null)
    {
        $foreign_key = isset($foreign_key) ? $foreign_key : $this->table . "_" . $primary_key;

        // get current data
        $query = "
            SELECT 
                " . ($this->alias ? $this->alias . "." : "") . "$primary_key
            FROM $this->table" . ($this->alias ? " AS " . $this->alias : "") . "
            " . str_replace("#current_table#", ($this->alias ? $this->alias : $this->table), $this->join) . "
            $this->where
            $this->orderBy
            $this->groupBy
            $this->limit
        ";
        $prepare = $this->db->query($query);
        $res = array_column($prepare->result(), $primary_key);
        // relation data
        $this->{$mode}($table, $res, $primary_key, $foreign_key);

        return $this;
    }

    /**
     * has one
     * @param string $table
     * @param array $res 
     * @param string $primary_key 
     * @param string $foreign_key 
     * @return void 
     */
    protected function hasOne($table, $res, $primary_key, $foreign_key)
    {
        $model = "m_" . $table;
        $this->load->model($model);
        $relation = new $model;

        $res = $relation->where([
            $foreign_key => "(" . implode(", ", $res) . ")"
        ], "in")->get();

        $with = [];
        foreach ($res as $key => $value) {
            $with[$table . "_" . $value->{$foreign_key}] = $value;
        }

        $this->with[] = [
            "data"    => $with,
            "primary" => $primary_key,
            "foreign" => $foreign_key,
            "table"   => $table
        ];
    }
    
    /**
     * has many
     * @param string $table
     * @param array $res 
     * @param string $primary_key 
     * @param string $foreign_key 
     * @return void 
     */
    protected function hasMany($table, $res, $primary_key, $foreign_key)
    {
        $model = "m_" . $table;
        $this->load->model($model);
        $relation = new $model;

        $res = $relation->where([
            $foreign_key => "(" . implode(", ", $res) . ")"
        ], "in")->get();

        $with = [];
        foreach ($res as $key => $value) {
            $with[$table . "_" . $value->{$foreign_key}][] = $value;
        }

        $this->with[] = [
            "data"    => $with,
            "primary" => $primary_key,
            "foreign" => $foreign_key,
            "table"   => $table
        ];
    }

}
