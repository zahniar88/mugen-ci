<?php
defined("BASEPATH") OR die("No direct access allowed");

class Validator
{

    /**
     * save request data
     *
     * @var string|array
     */
    protected $value;

    /**
     * save index name
     *
     * @var string
     */
    protected $index;

    /**
     * save alias name of index
     *
     * @var string
     */
    protected $alias;

    /**
     * save errors message
     *
     * @var array
     */
    public $errors = [];

    public function __construct() {
        $this->__ci =& get_instance();
    }

    /**
     * set validator
     *
     * @param string $key
     * @param array $rules
     * @return void
     */
    public function validate(Request $request, array $validate)
    {
        $this->__ci_req = $request;

        foreach ($validate as $index => $rules) {
            $indexKey = preg_replace("/[^a-zA-Z0-9_-]+$/", "", str_replace("-", "_", $index));
            $__req = $request->{$indexKey};
            $__is_files = isset($__req["__tmp_files"]);

            // validasi file
            if ( $__is_files && preg_match("/[^a-zA-Z0-9]+$/", $index) && is_array($__req["__tmp_files"]["name"]) ) {
                for ($i=0; $i < count($__req["__tmp_files"]["name"]); $i++) { 
                    $__getKeyName = array_keys($__req["__tmp_files"]["name"])[$i];
                    $this->value = $request->{$indexKey . "_" . $__getKeyName};
                    $this->index = $indexKey . "_" . $__getKeyName;
                    $this->alias = ucfirst($indexKey) . " " . $__getKeyName;

                    $this->runValidation($rules);
                }
            }
            elseif ( $__is_files && !preg_match("/[^a-zA-Z0-9]+$/", $index) && !is_array($__req["__tmp_files"]["name"]) ) {
                $this->value = $__req;
                $this->index = $indexKey;
                $this->alias = ucfirst($indexKey);

                $this->runValidation($rules);
            }

            // validasi string
            if ( !$__is_files && !preg_match("/[^a-zA-Z0-9]+$/", $index) && is_array($__req) ) {
                for ($i=0; $i < count($__req); $i++) {
                    $__getKeyName = array_keys($__req)[$i];
                    $this->value = $request->{$indexKey . "_" . $__getKeyName};
                    $this->index = $indexKey . "_" . $__getKeyName;
                    $this->alias = ucfirst($indexKey) . " " . $__getKeyName;

                    $this->runValidation($rules);
                }
            }
            elseif ( !$__is_files && preg_match("/[^a-zA-Z0-9]+$/", $index) && is_array($__req) ) {
                $this->value = $request->{$indexKey};
                $this->index = $indexKey;
                $this->alias = ucfirst($indexKey);

                $this->runValidation($rules);
            }
            elseif ( !$__is_files && !is_array($__req) ) {
                $this->value = $request->{$indexKey};
                $this->index = $indexKey;
                $this->alias = ucfirst($indexKey);

                $this->runValidation($rules);
            }
        }
    }

    /**
     * jalankan validasi
     *
     * @param array $rules
     * @return void
     */
    protected function runValidation($rules)
    {
        foreach ($rules as $rule) {
            $split  = explode(":", $rule);
            $func   = $split[0];
            $params = $split[1] ?? "";

            if ( $func == "nullable" && empty($this->value) ) {
                return;
            }

            if ( $func != "nullable" || empty($this->value) ) {
                $this->{$func}($params);
            }
        }
    }

    /**
     * validasi required
     *
     * @return void
     */
    protected function required()
    {
        /**
         * validasi data file array
         */
        if ( isset($this->value["__tmp_files"]) && strlen($this->value["__tmp_files"]["name"]) < 1 ) {
            $this->setError(":attribute wajib di isi.");
        }
        
        elseif ( !isset($this->value["__tmp_files"]) && !is_array($this->value) && strlen($this->value) < 1 ) {
            $this->setError(":attribute wajib di isi.");
        }
    }

    /**
     * validation alpha
     *
     * @return void
     */
    protected function alpha()
    {
        if ( !preg_match("/^[a-zA-Z]+$/", $this->value) ) {
            $this->setError("Isian :attribute hanya boleh berisi huruf.");
        }
    }
    
    /**
     * validation alpha_dash
     *
     * @return void
     */
    protected function alpha_dash()
    {
        if ( !preg_match("/^[a-zA-Z-]+$/", $this->value) ) {
            $this->setError("Isian :attribute hanya boleh berisi huruf dan strip.");
        }
    }
    
    /**
     * validation alpha_num
     *
     * @return void
     */
    protected function alpha_num()
    {
        if ( !preg_match("/^[a-zA-Z0-9]+$/", $this->value) ) {
            $this->setError("Isian :attribute hanya boleh berisi huruf dan angka.");
        }
    }
    
    /**
     * validation alpha_space
     *
     * @return void
     */
    protected function alpha_space()
    {
        if ( !preg_match("/^[a-zA-Z ]+$/", $this->value) ) {
            $this->setError("Isian :attribute hanya boleh berisi huruf dan spasi.");
        }
    }
    
    /**
     * validation alpha_num_space
     *
     * @return void
     */
    protected function alpha_num_space()
    {
        if ( !preg_match("/^[a-zA-Z0-9 ]+$/", $this->value) ) {
            $this->setError("Isian :attribute hanya boleh berisi huruf, angka dan spasi.");
        }
    }
    
    /**
     * validation numeric
     *
     * @return void
     */
    protected function numeric()
    {
        if ( !preg_match("/^[0-9]+$/", $this->value) ) {
            $this->setError("Isian :attribute harus berupa angka.");
        }
    }
    
    /**
     * validasi array
     *
     * @return void
     */
    protected function array()
    {
        if ( !is_array($this->value) ) {
            $this->setError("Isian :attribute harus berupa sebuah array.");
        }
    }

    /**
     * validasi between
     *
     * @param string $params
     * @return void
     */
    protected function between($params)
    {
        // extract
        $split = explode(",", $params);

        if ( 
            isset($this->value["__tmp_files"]) && 
            ($this->value["__tmp_files"]["size"] < $split[0] || $this->value["__tmp_files"]["size"] > $split[1]) 
        ) {
            $this->setError("Isian :attribute harus antara $split[0] dan $split[1] kilobytes.");
        }
        
        elseif ( 
            !isset($this->value["__tmp_files"]) && 
            is_array($this->value) && 
            (count($this->value) < $split[0] || count($this->value) > $split[1]) ) {
            $this->setError("Isian :attribute harus antara $split[0] dan $split[1] item.");
        }
        
        elseif ( 
            !isset($this->value["__tmp_files"]) && 
            !is_array($this->value) && 
            (strlen($this->value) < $split[0] || strlen($this->value) > $split[1]) ) {
            $this->setError("Isian :attribute harus antara $split[0] dan $split[1] karakter.");
        }
    }

    /**
     * validasi boolean
     *
     * @return void
     */
    protected function boolean()
    {
        if ( !filter_var($this->value, FILTER_VALIDATE_BOOLEAN) ) {
            $this->setError("Isian :attribute harus berupa true atau false.");
        }
    }

    /**
     * confirmed
     *
     * @return void
     */
    protected function distinct()
    {
        $unique = array_unique($this->value);

        if ( count($unique) < count($this->value) ) {
            $this->setError("Isian :attribute memiliki nilai yang duplikat.");
        }
    }

    /**
     * validate email
     *
     * @return void
     */
    protected function email()
    {
        if ( !filter_var($this->value, FILTER_VALIDATE_EMAIL) ) {
            $this->setError("Isian :attribute harus berupa alamat surel yang valid.");
        }
    }

    /**
     * validate in
     *
     * @param string $params
     * @return void
     */
    protected function in($params)
    {
        $split = explode(",", $params);
        if ( !in_array($this->value, $split) ) {
            $this->setError("Isian :attribute yang dipilih tidak valid.");
        }
    }
    
    /**
     * validate not_in
     *
     * @param string $params
     * @return void
     */
    protected function not_in($params)
    {
        $split = explode(",", $params);
        if ( in_array($this->value, $split) ) {
            $this->setError("Isian :attribute yang dipilih tidak valid.");
        }
    }

    /**
     * validate max
     *
     * @param string $params
     * @return void
     */
    protected function max($params)
    {
        if ( 
            isset($this->value["__tmp_files"]) && 
            ($this->value["__tmp_files"]["size"] > $params) 
        ) {
            $this->setError("Isian :attribute seharusnya tidak lebih dari $params kilobytes.");
        }
        
        elseif ( 
            !isset($this->value["__tmp_files"]) && 
            is_array($this->value) && 
            (count($this->value) > $params) ) {
            $this->setError("Isian :attribute seharusnya tidak lebih dari $params item.");
        }
        
        elseif ( 
            !isset($this->value["__tmp_files"]) && 
            !is_array($this->value) && 
            (strlen($this->value) > $params) ) {
            $this->setError("Isian :attribute seharusnya tidak lebih dari $params karakter.");
        }
    }

    /**
     * validate max
     *
     * @param string $params
     * @return void
     */
    protected function min($params)
    {
        if ( 
            isset($this->value["__tmp_files"]) && 
            ($this->value["__tmp_files"]["size"] < $params) 
        ) {
            $this->setError("Isian :attribute harus minimal $params kilobytes.");
        }
        
        elseif ( 
            !isset($this->value["__tmp_files"]) && 
            is_array($this->value) && 
            (count($this->value) < $params) ) {
            $this->setError("Isian :attribute harus minimal $params item.");
        }
        
        elseif ( 
            !isset($this->value["__tmp_files"]) && 
            !is_array($this->value) && 
            (strlen($this->value) < $params) ) {
            $this->setError("Isian :attribute harus minimal $params karakter.");
        }
    }

    /**
     * validate mimes
     *
     * @param string $params
     * @return void
     */
    protected function mimes($params)
    {
        $split = explode(",", $params);
        $extension = pathinfo($this->value["__tmp_files"]["name"], PATHINFO_EXTENSION);

        if ( !in_array($extension, $split) ) {
            $this->setError("Isian :attribute harus dokumen berjenis : $params.");
        }
    }

    /**
     * validate regex
     *
     * @param string $params
     * @return void
     */
    protected function regex($params)
    {
        if ( !preg_match("$params", $this->value) ) {
            $this->setError("Format isian :attribute tidak valid.");
        }
    }

    /**
     * validate email
     *
     * @return void
     */
    protected function url()
    {
        if ( !filter_var($this->value, FILTER_VALIDATE_URL) ) {
            $this->setError("Format isian :attribute harus alamat URL yang valid.");
        }
    }

    /**
     * validate exists
     *
     * @param string $params
     * @return void
     */
    protected function exists($params)
    {
        $split = explode(",", $params);

        $this->__ci->load->model("m_validation");
        $res = $this->__ci->m_validation->exists($split[0], $split[1], $this->value);

        if ( $res < 1 ) {
            $this->setError("Isian :attribute yang dipilih tidak tidak terdaftar.");
        }
    }
    
    /**
     * validate unique
     *
     * @param string $params
     * @return void
     */
    protected function unique($params)
    {
        $split      = explode(",", $params);
        $except     = $split[2] ?? "";
        $except_val = $split[3] ?? "";

        $this->__ci->load->model("m_validation");
        $res = $this->__ci->m_validation->unique($split[0], $split[1], $this->value, $except, $except_val);

        if ( $res > 0 ) {
            $this->setError("Isian :attribute sudah ada sebelumnya.");
        }
    }

    /**
     * validate confirmed
     *
     * @return void
     */
    protected function confirmed()
    {
        if ( $this->value != $this->__ci_req->{$this->index . "_confirm"} ) {
            $this->setError("Konfirmasi :attribute tidak cocok.");
        }
    }
    
    /**
     * validate same
     *
     * @return void
     */
    protected function same($params)
    {
        if ( $this->value != $this->__ci_req->{$params} ) {
            $this->setError("Isian :attribute dan $params harus sama.");
        }
    }
    
    /**
     * validate password
     *
     * @return void
     */
    protected function password()
    {
        $errors = [];
        if ( !preg_match("/[a-z]+/", $this->value) ) {
            array_push($errors, "huruf kecil");
        }
        
        if ( !preg_match("/[A-Z]+/", $this->value) ) {
            array_push($errors, "huruf besar");
        }
        
        if ( !preg_match("/[0-9]+/", $this->value) ) {
            array_push($errors, "angka");
        }
        
        if ( !preg_match("/[^a-zA-Z0-9]+/", $this->value) ) {
            array_push($errors, "simbol");
        }

        if (count($errors) > 0) {
            $this->setError("Isian :attribute setidaknya mengandung " . implode(", ", $errors) . ".");
        }
    }

    /**
     * set error message
     *
     * @param string $message
     * @return void
     */
    protected function setError($message)
    {
        if ( !array_key_exists($this->index, $this->errors) ) {
            $this->errors[$this->index] = ucfirst(str_replace(":attribute", strtolower($this->alias), $message));
        }
    }

}