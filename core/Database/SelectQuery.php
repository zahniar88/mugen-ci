<?php
defined("BASEPATH") OR die("No direct access allowed");

/**
 * 
 */
trait SelectQuery
{

    /**
     * generate raw query
     * @param mixed $query 
     * @return $this 
     */
    public function selectRaw($query)
    {
        if ( is_array($query) ) {
            $this->cols = array_merge($this->cols, $query);
        }

        else {
            array_push($this->cols, $query);
        }

        return $this;
    }

    /**
     * set column
     * @param array $query 
     * @return $this 
     */
    public function setCols($query = [])
    {
        $this->cols = $query;
        return $this;
    }

    /**
     * generate query where
     * 
     * @param mixed $query 
     * @param string $operator 
     * @param string $separator 
     * @return $this 
     */
    public function where($query, $operator = "=", $separator = "AND")
    {
        $where = " WHERE ";
        if ( is_array($query) ) {
            $hasWhere = [];
            foreach ($query as $col => $value) {
                $hasWhere[] = "$col $operator '$value'";
            }

            $where .= implode(" " . $separator . " ", $hasWhere);
        }

        else {
            $where .= $query;
        }

        $this->where = $where;

        return $this;
    }

    /**
     * generate query orderBy
     * @param mixed $column 
     * @param string $method 
     * @return $this 
     */
    public function orderBy($column, $method = "ASC")
    {
        $this->orderBy = " ORDER BY $column $method";

        return $this;
    }

    /**
     * generate query group by
     * @param string $column 
     * @return $this 
     */
    public function groupBy($column = [])
    {
        $this->groupBy = " GROUP BY " . implode(", ", $column);
        return $this;
    }

    /**
     * generate query limit
     * @param mixed $length 
     * @param mixed|null $offset 
     * @return $this 
     */
    public function limit($length, $offset = null)
    {
        $limit = " LIMIT " . $length;
        if ( isset($offset) ) {
            $limit .= " OFFSET " . $offset;
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * generate query result
     * @return void 
     */
    public function get()
    {
        $query = "
            SELECT 
                " . implode(", ", $this->cols) . "
            FROM $this->table
            $this->where
            $this->orderBy
            $this->groupBy
            $this->limit
        ";

        // $prepare = $this->db->query($query);
        // $res = $prepare->result_array();

        // return $this->response($res);
        return $query;
    }

    /**
     * convert result to single array
     * @return $this 
     */
    public function single()
    {
        $this->single = true;
        return $this;
    }

    /**
     * generate response
     * @param mixed $results 
     * @return mixed 
     */
    protected function response($results)
    {
        $results = $this->has_hidden($results);
        $results = $this->valueToObject($results);

        // if result is single data
        if ( property_exists($this, "single") && $this->single ) {
            return $results[0];
        }

        return $results;
    }

    /**
     * reset hidden collumns
     * @return $this 
     */
    public function noHide()
    {
        $this->hidden = [];
        return $this;
    }

    /**
     * convert value to object
     * @param mixed $results 
     * @return mixed 
     */
    protected function valueToObject($results)
    {
        foreach ($results as $key => $value) {
            $results[$key] = (object) $value;
        }

        return $results;
    }

    /**
     * has hidden
     * @param mixed $results 
     * @return mixed 
     */
    protected function has_hidden($results)
    {
        if ( property_exists($this, "hidden") ) {
            $hidden = $this->hidden;
            foreach ($results as &$res) {
                $res = array_filter((array) $res, function($val, $key) use ($hidden) {
                    return !in_array($key, $hidden);
                }, ARRAY_FILTER_USE_BOTH);
            }
        }

        return $results;
    }

}
