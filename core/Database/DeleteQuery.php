<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait DeleteQuery
{
    
    /**
     * delete method
     * @return bool 
     */
    public function destroy()
    {
        $query = "
            DELETE FROM $this->table
            $this->where
        ";
        $prepare = $this->db->query($query);

        return $prepare ? true : false;
    }

}
