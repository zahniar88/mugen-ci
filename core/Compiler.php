<?php 
defined("BASEPATH") OR die("No direct access allowed");

class Compiler
{

    /**
     * get ;ast modified file
     *
     * @param string $path
     * @return void
     */
    protected function lastModified(string $path)
    {
        return filemtime($path);
    }

    /**
     * get compiled path
     *
     * @param string $path
     * @return string
     */
    protected function getCompiledPath(string $path)
    {
        return $this->getCachePath() . "/" . sha1($path) . ".php";
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function getCachePath()
    {
        $cacheDir = APPPATH . "/cache/views";
        if ( !is_dir($cacheDir) ) {
            mkdir($cacheDir);
        }

        return $cacheDir;
    }

}