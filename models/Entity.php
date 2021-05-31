<?php
defined("BASEPATH") OR die("No direct access allowed");

require_once __DIR__ . "/../core/Database/InsertQuery.php";

class Entity extends CI_Model {

    use InsertQuery;

    /**
     * define default table name
     * @var string
     */
    protected $table = "";

    /**
     * define default cols name
     * @var array
     */
    protected $cols = [];

    /**
     * define default hidden columns 
     * @var array
     */
    protected $hidden = [];

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