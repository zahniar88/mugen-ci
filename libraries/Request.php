<?php
defined("BASEPATH") OR die("No direct access allowed");

class Request
{

    /**
     * set constructor property
     */
    public function __construct() {
        $__req_ci = array_merge($_REQUEST, $_FILES);

        foreach ($__req_ci as $key => $val) {
            if ( isset($_FILES[$key]) ) {
                $this->setFilesProperty(str_replace("-", "_", $key), $val);
            } else {
                $this->setStringProperty(str_replace("-", "_", $key), $val);
            }
        }
    }

    /**
     * set property of key value request
     * @param string $key
     * @param array|string $val
     *
     * @return void
     */
    protected function setStringProperty(string $key, $val)
    {
        if ( is_array($val) ) {
            for ($i = 0; $i < count($val); $i++) {
                $__getKeyName = array_keys($val)[$i];
                $__key = $key . "_" . str_replace("-", "_", $__getKeyName);

                $this->{$__key} = $val[$__getKeyName];
                $this->{$key} = $val;
            }
        } else {
            $this->{$key} = $val;
        }
    }

    /**
     * set property of key value files
     *
     * @param string $key
     * @param array $val
     * @return void
     */
    protected function setFilesProperty(string $key, array $val)
    {
        if ( is_array($val["name"]) ) {
            $this->{$key} = ["__tmp_files" => $val];
            
            for ($i=0; $i < count($val["name"]); $i++) { 
                $__getKeyName = array_keys($val["name"])[$i];
                $__key = $key . "_" . str_replace("-", "_", $__getKeyName);
                
                $this->{$__key} = [
                    "__tmp_files" => [
                        "name"     => $val["name"][$__getKeyName],
                        "tmp_name" => $val["tmp_name"][$__getKeyName],
                        "size"     => $val["size"][$__getKeyName],
                        "error"    => $val["error"][$__getKeyName],
                        "type"     => $val["type"][$__getKeyName],
                    ]
                ];
            }
        } else {
            $this->{$key} = ["__tmp_files" => $val];
        }
    }

    /**
     * setter request value
     *
     * @param string $key
     * @return void
     */
    public function __get(string $key)
    {
        if ( !property_exists($this, $key) ) {
            $this->{$key} = "";
        }
    }

    /**
     * get of file data
     *
     * @param string $key
     * @return void
     */
    public function file(string $key)
    {
        $this->files = $this->{$key}["__tmp_files"];
        return $this;
    }

    /**
     * get extension file
     *
     * @return void
     */
    public function extension()
    {
        return pathinfo($this->files["name"], PATHINFO_EXTENSION);
    }

    /**
     * get filename
     *
     * @return void
     */
    public function fileName()
    {
        return pathinfo($this->files["name"], PATHINFO_FILENAME);
    }

    /**
     * store file
     *
     * @return void
     */
    public function storeAs(string $dir = "", string $rename = "")
    {
        if ( empty($rename) ) {
            $fileName = $this->files["name"];
        } else {
            $fileName = $rename;
        }

        if ( move_uploaded_file($this->files["tmp_name"], $dir . $fileName) ) {
            $uploadedFile = $dir . $fileName;
        } else {
            $uploadedFile = "";
        }

        return $uploadedFile;
    }

}