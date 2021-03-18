<?php
defined("BASEPATH") or die("No Direct Access Allowed");

trait EchoCompiler
{

    /**
     * echo tag with escaped
     *
     * @var array
     */
    protected $echoEscapeTag = ["{{", "}}"];

    /**
     * echo tag without escaped
     *
     * @var array
     */
    protected $echoTag = ["{!!", "!!}"];

    /**
     * compile echo tag with escaped html markup
     *
     * @param string $content
     * @return void
     */
    protected function echoWithEscape($content)
    {
        $pattern = sprintf("/%s\s*(.*?)\s*%s/s", $this->echoEscapeTag[0], $this->echoEscapeTag[1]);
        $callback = function ($matches) {
            return "<?php echo htmlspecialchars(" . $matches[1] . "); ?>";
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * compile echo tag without escaped html markup
     *
     * @param string $content
     * @return void
     */
    protected function echoWithoutEscape($content)
    {
        $pattern = sprintf("/%s\s*(.*?)\s*%s/s", $this->echoTag[0], $this->echoTag[1]);
        $callback = function ($matches) {
            return "<?php echo " . $matches[1] . "; ?>";
        };

        return preg_replace_callback($pattern, $callback, $content);
    }
}
