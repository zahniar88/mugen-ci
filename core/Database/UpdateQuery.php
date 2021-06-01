<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait UpdateQuery
{
    
    /**
     * query updating
     * @param array $params 
     * @return array|false 
     */
    public function update($params = [])
    {
        $fillable = $this->fillable;
        $params = array_filter($params, function($val, $key) use ($fillable) {
            return in_array($key, $fillable);
        }, ARRAY_FILTER_USE_BOTH);

        if ( $this->timestamps ) {
            $params["updated_at"] = $this->current_time();
        }

        $update_cols = [];
        foreach ($params as $key => $val) {
            $update_cols[] = "$key = '" . $val . "'";
        }

        $query = "
            UPDATE $this->table
                SET " . implode(", \n", $update_cols) . "
            $this->where
        ";
        $prepare = $this->db->query($query);

        return $prepare ? $params : false;
    }

}
