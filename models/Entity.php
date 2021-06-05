<?php
defined("BASEPATH") OR die("No direct access allowed");

require_once __DIR__ . "/../core/Database/SelectQueryBuilder.php";
require_once __DIR__ . "/../core/Database/InsertQueryBuilder.php";
require_once __DIR__ . "/../core/Database/UpdateQueryBuilder.php";
require_once __DIR__ . "/../core/Database/DeleteQueryBuilder.php";

class Entity extends CI_Model {    

    use SelectQueryBuilder,
        InsertQueryBuilder,
        UpdateQueryBuilder,
        DeleteQueryBuilder;

    /**
     * define default table name
     * @var string
     */
    protected $table = "";

    /**
     * define column can insert or update
     * @var array
     */
    protected $fillable = [];

    /**
     * define default timestamp
     * @var true
     */
    protected $timestamps = true;

    /**
     * save where query fro global
     * @var string
     */
    protected $where = "";

    /**
     * get default time
     * @return string 
     */
    public function current_time()
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * make query where
     * @param array|string $where 
     * @param string $operator 
     * @param string $separator 
     * @return $this 
     */
    public function where($where, $operator = "=", $separator = "AND")
    {
        $query = " WHERE ";
        if ( is_array($where) ) {
            $expr = [];
            foreach ($where as $key => $value) {
                $expr[] = "$key $operator $value";
            }
            $query .= implode($separator, $expr);
        }
        else {
            $query .= $where;
        }

        $this->where = $query;

        return $this;
    }

}