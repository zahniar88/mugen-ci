<?php
defined("BASEPATH") OR die("No direct access allowed");

class Datatables extends Entity
{
    
    /**
     * set table name
     * @param string $table 
     * @return $this 
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * where columns can find
     * @param array $columns 
     * @return $this
     */
    public function canFind(callable $callback)
    {
        $search = $this->request->search["value"];
        $this->where($callback("%" . $search . "%"), "LIKE", "OR");
        return $this;
    }

    /**
     * render view
     * @return void 
     */
    public function render(callable $callback = null)
    {
        // getting information
        $draw   = $this->request->draw;
        $start  = $this->request->start;
        $length = $this->request->length;
        $order  = $this->request->order[0]["column"];
        $dir    = $this->request->order[0]["dir"];
        $column = $this->request->columns[$order]["name"];

        $define = $this->orderBy($column, $dir);

        $count = $define->count();
        $results = $define->limit($length, $start)->get();

        // modify column or adding column
        if ( is_callable($callback) ) {
            $callback($results);
        }

        echo json_encode([
            "draw"            => $draw,
            "recordsTotal"    => $count,
            "recordsFiltered" => $count,
            "data"            => $results
        ], JSON_PRETTY_PRINT);
    }

}
