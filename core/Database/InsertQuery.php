<?php
defined("BASEPATH") OR die("No direct access allowed");

trait InsertQuery {

    /**
     * default query create / insert data
     * 
     * @param mixed $params 
     * @return mixed 
     */
    public function create($params)
    {
        $cols = [];

        // set cols name
        foreach ($params as $key => $value) {
            if ( in_array($key, $this->fillable) ) {
                $cols[$key] = "'" . $value . "'";
            }
        }

        // if have timestamp
        if ( $this->timestamps ) {
            $cols["created_at"] = "'" . $this->current_time() . "'";
        }

        $query = "
            INSERT INTO $this->table
                (". implode(", ", array_keys($cols)) .")
            VALUES
                (". implode(", ", array_values($cols)) .")
        ";
        $prepare = $this->db->query($query);
        
        return $prepare ? $params : false;
    }

}