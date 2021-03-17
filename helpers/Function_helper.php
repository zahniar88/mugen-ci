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