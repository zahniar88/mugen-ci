<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait DeleteQueryBuilder
{
    
    /**
     * delete value
     * @return bool 
     */
    public function delete()
    {
        $data = $this->get();

        $query = "
            DELETE FROM $this->table
            $this->where
        ";
        $prepare = $this->db->query($query);

        return $prepare ? $data : false;
    }

}
