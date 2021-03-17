<?php
defined("BASEPATH") OR die("No direct access allowed");

class M_validation extends CI_Model
{

    /**
     * get exists data
     *
     * @param string $table
     * @param string $column
     * @return void
     */
    public function exists($table, $column, $value)
    {
        $query = "
            SELECT COUNT($column) as $column FROM $table WHERE $column='$value'
        ";
        $stmt = $this->db->query($query);
        $res = $stmt->row();

        return $res->$column;
    }
    
    /**
     * get unique data
     *
     * @param string $table
     * @param string $column
     * @return void
     */
    public function unique($table, $column, $value, $except = "", $except_val = "")
    {
        $query = "
            SELECT COUNT($column) as $column FROM $table WHERE $column='$value'
        ";

        if ( !empty($except_val) ) {
            $query .= " AND $except != '$except_val'";
        }

        $stmt = $this->db->query($query);
        $res = $stmt->row();

        return $res->$column;
    }

}