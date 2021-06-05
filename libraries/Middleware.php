<?php
defined("BASEPATH") OR die("No direct access allowed");

class Middleware
{

    /**
     * save instance ci loader
     * @var mixed
     */
    protected $__ci = null;
    
    public function __construct() {
        $this->__ci =& get_instance();
    }

    /**
     * request must be ajax
     * @return bool 
     */
    public function ajax()
    {
        if ( !$this->__ci->input->is_ajax_request() ) {
            echo toJson([
                "status" => "method_not_allowed",
                "message" => "Your request must be ajax"
            ]);
            return false;
        }

        return true;
    }

    /**
     * signin user
     * @param array|object $params 
     * @return void 
     */
    public function signin($params)
    {
        $this->__ci->session->set_userdata("user", $params);
    }
    
    /**
     * removing session user
     * @return void 
     */
    public function signout()
    {
        $this->__ci->session->unset_userdata("user");
    }

    /**
     * auth
     * @return bool 
     */
    public function auth()
    {
        if ( $this->__ci->input->is_ajax_request() && !user() ) {
            echo toJson([
                "status" => "Unauthorized"
            ]);
            return false;
        }

        if ( !$this->__ci->input->is_ajax_request() && !user() ) {
            return false;
        }

        return true;
    }
    
    /**
     * for guest
     * @return bool 
     */
    public function guest()
    {
        if ( $this->__ci->input->is_ajax_request() && user() ) {
            echo toJson([
                "status" => "has_authorized"
            ]);
            return false;
        }

        if ( !$this->__ci->input->is_ajax_request() && user() ) {
            return false;
        }

        return true;
    }

}
