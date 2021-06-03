<?php
defined("BASEPATH") OR die("No direct access allowed");

require_once __DIR__ . "/JoinQueryBuilder.php";

/**
 * 
 */
trait SelectQueryBuilder
{
    use JoinQueryBuilder;

    /**
     * saving query
     * @var string[]
     */
    protected $select         = ["*"],
              $selectRaw      = [],
              $relationSelect = [],
              $join           = "",
              $where          = "",
              $orderBy        = "",
              $groupBy        = "",
              $limit          = "",
              $single         = false;

    /**
     * add select raw
     * @param mixed $select 
     * @return $this 
     */
    public function selectRaw($select)
    {
        if ( is_array($select) ) {
            $this->selectRaw = array_merge($select, $this->selectRaw);
        }
        else {
            array_push($this->selectRaw, $select);
        }

        return $this;
    }

    /**
     * make query where
     * @param mixed $query 
     * @param string $operator 
     * @param string $separator 
     * @return $this 
     */
    public function where($query, $operator = "=", $separator = "AND")
    {
        $where = " WHERE ";
        if ( is_array($query) ) {
            $cols = [];
            foreach ($query as $key => $value) {
                $cols[] = "$key $operator $value";
            }

            $where .= implode(" " . $separator . " ", $cols);
        }

        else {
            $where .= $query;
        }

        $this->where = $where;
        return $this;
    }

    /**
     * make order by query
     * @param mixed $column 
     * @param string $type 
     * @return $this 
     */
    public function orderBy($column, $type = "ASC")
    {
        $this->orderBy = " ORDER BY $column $type";
        return $this;
    }
    
    /**
     * make query group by
     * @param mixed $column 
     * @return $this 
     */
    public function groupBy($column)
    {
        $groupBy = " GROUP BY ";
        if ( is_array($column) ) {
            $groupBy .= implode(", ", $column);
        }
        
        else {
            $groupBy .= $column;
        }

        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * make query limit
     * @param mixed $limit 
     * @param mixed|null $offset 
     * @return $this 
     */
    public function limit($limit, $offset = null)
    {
        $limit = " LIMIT $limit";

        if ( isset($offset) ) {
            $limit .= " OFFSET $offset";
        }

        $this->limit = $limit;
        return $this;
    }

    /**
     * getting data
     * @return mixed 
     */
    public function get()
    {
        if ( $this->join ) {
            $this->select = array_merge($this->getCurrentColumns(), $this->relationSelect);
        }

        $query = "
            SELECT 
                " . implode(", ", array_merge($this->select, $this->selectRaw)) . "
            FROM $this->table
            $this->join
            $this->where
            $this->orderBy
            $this->groupBy
            $this->limit
        ";

        $prepare = $this->db->query($query);
        $res = $prepare->result_array();

        return $this->response($res);
    }

    /**
     * return first single value
     * @return $this 
     */
    public function single()
    {
        $this->single = true;
        return $this;
    }


    protected function response($results)
    {
        // convert to object
        $results = array_map(function($val) {
            return (object) $val;
        }, $results);

        if ( $this->single ) {
            $results = $results[0];
        }
        
        return $results;
    }
    
}
