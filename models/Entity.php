<?php
defined("BASEPATH") OR die("No direct access allowed");

require_once __DIR__ . "/../core/Database/SelectQueryBuilder.php";

class Entity extends CI_Model {    

    use SelectQueryBuilder;

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
     * get default time
     * @return string 
     */
    public function current_time()
    {
        return date("Y-m-d H:i:s");
    }

}