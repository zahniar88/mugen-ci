<?php
/**
 * set old data
 */
if ( !function_exists("setFlashData") ) {
    
    function setFlashData(Request $data) {
        $__ci =& get_instance();
        $__ci->load->library("session");

        foreach ($data as $key => $value) {
            $__ci->session->set_flashdata($key, $value);
        }
    }

}

/**
 * get old data
 */
if ( !function_exists("old") ) {
    
    function old(string $key) {
        $__ci =& get_instance();
        $__ci->load->library("session");

        return $__ci->session->flashdata($key);
    }

}

/**
 * make response ajax
 */
if ( !function_exists("response_json") ) {
    
    function toJson($response = []) {
        return json_encode($response, JSON_PRETTY_PRINT);
    }

}

/**
 * get data user in authentication
 */
if ( !function_exists("user") ) {
    
    function user() {
        $__ci =& get_instance();
        if ( isset($__ci->session->user) ) {
            return $__ci->session->user;
        }

        return false;
    }

}

/**
 * middleware
 */
if ( !function_exists("middleware") ) {
    
    function middleware() {
        $__ci =& get_instance();
        $__ci->load->library("middleware");
        return $__ci->middleware;
    }

}

/**
 * request from datatables
 */
if ( !function_exists("datatables_request") ) {

    function datatables_request() {
        $__ci =& get_instance();

        // getting information
        return [
            "draw"   => $__ci->request->draw,
            "start"  => $__ci->request->start,
            "length" => $__ci->request->length,
            "search" => $__ci->request->search["value"],
            "order"  => $__ci->request->order[0]["column"],
            "dir"    => $__ci->request->order[0]["dir"],
            "column" => $__ci->request->columns[$__ci->request->order[0]["column"]]["name"],
        ];
    }

}