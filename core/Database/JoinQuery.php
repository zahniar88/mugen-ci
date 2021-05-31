<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait JoinQuery
{

    /**
     * save object join
     * @var array
     */
    protected $joins = [];
    
    /**
     * define join table
     * 
     * @param array $tables 
     * @return $this 
     */
    public function with($tables = [])
    {
        foreach ($tables as $key) {
            $this->joins[$key] = $this->{$key}();
        }

        return $this;
    }

    /**
     * join one to one
     * @param mixed $model 
     * @param string $primary_key 
     * @param mixed|null $foreign_key 
     * @return array 
     */
    protected function hasOne($model, $primary_key = "id", $foreign_key = null)
    {
        $this->load->model($model);
        $table = new $model;

        $foreign_key = isset($foreign_key) ? $foreign_key : $this->table . "_" . $primary_key;

        // parent data
        $parents = $this->getParents($primary_key);

        // children data
        $children = $table->where("$foreign_key in (" . implode(", ", $parents) . ")")->get();
        $results = [];

        foreach ($children as $key => $value) {
            $results["data"][str_replace("m_", "", $model) . "_" . $value->user_id] = $value;
        }
        $results["primary"] = $primary_key;
        $results["foreign"] = $foreign_key;
        $results["method"]  = "hasOne";
        $results["table"]   = str_replace("m_", "", $model);


        return $results;
    }
    
    /**
     * join to many
     * @param mixed $model 
     * @param string $primary_key 
     * @param mixed|null $foreign_key 
     * @return array 
     */
    protected function hasMany($model, $primary_key = "id", $foreign_key = null)
    {
        $this->load->model($model);
        $table = new $model;

        $foreign_key = isset($foreign_key) ? $foreign_key : $this->table . "_" . $primary_key;

        // parent data
        $parents = $this->getParents($primary_key);

        // children data
        $children = $table->where("$foreign_key in (" . implode(", ", $parents) . ")")->get();
        $results = [];

        foreach ($children as $key => $value) {
            $results["data"][str_replace("m_", "", $model) . "_" . $value->user_id][] = $value;
        }
        $results["primary"] = $primary_key;
        $results["foreign"] = $foreign_key;
        $results["method"]  = "hasMany";
        $results["table"]   = str_replace("m_", "", $model);


        return $results;
    }

    /**
     * get parents data
     * @param mixed $primary_key 
     * @return array 
     */
    protected function getParents($primary_key)
    {
        $query = "
            SELECT 
                $primary_key 
            FROM $this->table 
            $this->where
            $this->orderBy
            $this->groupBy
            $this->limit
        ";

        $prepare = $this->db->query($query);
        $parents = array_column($prepare->result(), "id");

        return $parents;
    }

}
