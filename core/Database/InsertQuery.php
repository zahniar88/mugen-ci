<?php
defined("BASEPATH") OR die("No direct access allowed");

trait InsertQuery {

    /**
     * default create
     * @param array $params 
     * @return array|false 
     */
    public function create($params = [])
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

    /**
     * create many
     * @param array $params 
     * @return array|false 
     */
    public function createMany($params = [])
    {
        $values = [];
        foreach ($params as &$param) {
            // if have timestamp
            if ($this->timestamps) {
                $param["created_at"] = $this->current_time();
            }

            $param = array_map(function ($val) {
                return "'" . $val . "'";
            }, $param);
            $values[] = "(" . implode(", ", array_values($param)) . ")";
        }

        $cols = array_keys($params[0]);

        $query = "
            INSERT INTO $this->table 
                (" . implode(", ", $cols) . ")
            VALUES"
            . implode(", \n", $values);

        $prepare = $this->db->query($query);

        return $prepare ? $params : false;

    }

}