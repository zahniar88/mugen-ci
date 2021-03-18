<?php
defined("BASEPATH") or die("No direct Access Allowed");

trait PhpTagCompiler
{

    /**
     * compile php tag
     *
     * @param string $content
     * @return void
     */
    protected function phpTag($content)
    {
        $pattern = sprintf("/%s\s*(.*?)\s*%s/s", "@php", "@endphp");
        $callback = function ($matches) {
            return "<?php $matches[1] ?>";
        };

        return preg_replace_callback($pattern, $callback, $content);
    }
}
