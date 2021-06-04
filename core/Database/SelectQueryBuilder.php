<?php
defined("BASEPATH") OR die("No direct access allowed");

require_once  __DIR__ . "/JoinQueryBuilder.php";
require_once  __DIR__ . "/WithQueryBuilder.php";

/**
 * 
 */
trait SelectQueryBuilder
{
    use JoinQueryBuilder, WithQueryBuilder;
 
    /**
     * saving query
     * @var array
     */
    protected $columns   = ["*"],
              $selectRaw = [],
              $select    = [],
              $join      = "",
              $orderBy   = "",
              $groupBy   = "",
              $limit     = "",
              $alias     = "",
              $single    = false;
    
    /**
     * select column on database
     * @param array|string $select 
     * @return $this 
     */
    public function select($select)
    {
        if ( is_array($select) ) {
            $this->select = $select;
        }
        else {
            $this->select = explode(",", $select);
        }
        return $this;
    }

    /**
     * add select raw
     * @param array|string $selectRaw 
     * @return $this 
     */
    public function selectRaw($selectRaw)
    {
        if ( is_array($selectRaw) ) {
            $this->selectRaw = $selectRaw;
        }
        else {
            $this->selectRaw = explode(",", $selectRaw);
        }
        return $this;
    }

    /**
     * make query order by
     * @param array|string $column 
     * @param string $type 
     * @return $this 
     */
    public function orderBy($column, $type = null)
    {
        $orderBy = " ORDER BY ";
        if ( is_array($column) ) {
            $orderBy .= implode(", ", $column);
        }
        else {
            $orderBy .= $column;
        }

        $this->orderBy = $orderBy . " " . $type;

        return $this;
    }
    
    /**
     * make query group by
     * @param array|string $column 
     * @return $this 
     */
    public function groupBy($column)
    {
        $groupBy = " ORDER BY ";
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
     * 
     * @param int $limit 
     * @param int|null $offset 
     * @return $this 
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = " LIMIT $limit " . (isset($offset) ? "OFFSET $offset" : "");

        return $this;
    }

    /**
     * set alias current table name
     * @param string $alias 
     * @return $this 
     */
    public function alias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * unhide culumn
     * @return $this 
     */
    public function unhide()
    {
        $this->hidden = [];
        return $this;
    }

    /**
     * return to single result
     * @return $this 
     */
    public function single()
    {
        $this->single = true;
        return $this;
    }

    /**
     * get count of data
     * @return mixed 
     */
    public function count()
    {
        $query = "
            SELECT
                COUNT(*) AS count
            FROM $this->table
            $this->join
            $this->where
            $this->orderBy
            $this->groupBy
        ";
        $prepare = $this->db->query($query);
        $res = $prepare->row();

        return $res->count;
    }

    /**
     * getting query
     * @return array 
     */
    public function get()
    {
        // set column if have join
        if ( $this->join ) {
            $this->columns = array_merge($this->getCurrentColumns(), $this->joinColumns);
        }

        // make select column
        $this->columns = array_merge((count($this->select) > 0 ? $this->select : $this->columns), $this->selectRaw);
        $query = "
            SELECT
                " . implode(", ", $this->columns) . "
            FROM $this->table" . ($this->alias ? " AS " . $this->alias : "") . "
            " . str_replace("#current_table#", ($this->alias ? $this->alias : $this->table), $this->join) . "
            $this->where
            $this->orderBy
            $this->groupBy
            $this->limit
        ";

        $prepare = $this->db->query($query);
        return $this->response($prepare->result_array());
    }


    /**
     * response result
     * @param array $results 
     * @return mixed 
     */
    protected function response($results = [])
    {
        $results = array_map(function($val) {
            return (object) $val;
        }, $results);

        if ( $this->with ) {
            foreach ($this->with as $key => $value) {
                foreach ($results as &$result) {
                    $key = $value["table"] . "_" . $result->{$value["primary"]};
                    $result->{$value["table"]} = $value["data"][$key] ?? null;
                }
            }
        }

        return $this->single ? $results[0] : $results;
    }

}
