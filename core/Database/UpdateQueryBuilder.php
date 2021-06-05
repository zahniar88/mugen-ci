<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait UpdateQueryBuilder
{
    
    /**
     * make update query
     * @param array $params 
     * @return array|false 
     */
    public function update($params = [])
    {
        // filtering params
        $fillable = $this->fillable;
        $params = array_filter($params, function($val, $key) use ($fillable) {
            return in_array($key, $fillable);
        }, ARRAY_FILTER_USE_BOTH);

        // mapping
        $data = array_map(function($val) {
            return "'$val'";
        }, $params);

        // timestamps
        if ( $this->timestamps ) {
            $data["updated_at"] = $this->current_time();
        }

        // set up set columns
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = $value";
        }

        $query = "
            UPDATE $this->table
            SET " . implode(", \n", $sets) . "
            $this->where
        ";
        $prepare = $this->db->query($query);

        return $prepare ? $params : false;
    }

}
