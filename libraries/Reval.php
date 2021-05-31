<?php
defined("BASEPATH") OR die("No direct access allowed");

require_once __DIR__ . "/../core/EngineCompiler/EchoCompiler.php";
require_once __DIR__ . "/../core/EngineCompiler/PhpTagCompiler.php";
require_once __DIR__ . "/../core/EngineCompiler/RevalCompiler.php";
require_once __DIR__ . "/../core/Compiler.php";

class Reval extends Compiler
{

    use EchoCompiler, PhpTagCompiler, RevalCompiler;

    /**
     * get the view object
     *
     * @param string $view
     * @param array $params
     * @return void
     */
    public function view(string $path, array $params = [])
    {
        $path = str_replace(".", "/", $path);
        $_viewFile = VIEWPATH . "/" . $path . ".blade.php";
        
        /**
         * load key of array as a variable
         */
        if ( !empty($params) ) {
            foreach ($params as $key => $value) {
                ${$key} = $value;
            }
        }

        if ( file_exists($_viewFile) ) {

            // This allows anything loaded using $this->load (views, files, etc.)
            // to become accessible from within the Controller and Model functions.
            $_ci_CI = &get_instance();
            foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
                if (!isset($this->$_ci_key)) {
                    $this->$_ci_key = &$_ci_CI->$_ci_key;
                }
            }
            
            $compiled = $this->getCompiledPath($path);
            if ( !file_exists($compiled) || ($this->lastModified($_viewFile) >= $this->lastModified($compiled)) ) {
                // load content
                $content = file_get_contents($_viewFile);

                // parse view
                $content = $this->render($content);

                $file = fopen($compiled, "w+");
                fwrite($file, $content);
                fclose($file);
            }
            
            require_once $compiled;
        } else {
            show_error("Unable to load the requested file: " . $_viewFile, 404, "404 - File Not Found");
        }
    }

    /**
     * rendering view
     * 
     * @param mixed $content 
     * @return mixed 
     */
    protected function render($content)
    {
        // method loader
        $methods = [
            "CommentCompiler",
            "echoWithEscape",
            "echoWithoutEscape",
            "phpTag",
            "compileStatements",
            "compileConditionalLooping",
            "functionViewCompiler",
        ];

        foreach ($methods as $method) {
            $content = $this->{$method}($content);
        }

        return $content;
    }

}