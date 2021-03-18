<?php
defined("BASEPATH") OR die("No Direct Access Allowed");

trait MugenCompiler
{

    protected $explicitOpenTag = [
        "if",
        "for",
        "while",
        "elseif",
        "foreach"
    ];
    
    protected $explicitCloseTag = [
        "endif",
        "endfor",
        "endwhile",
        "endforeach",
        "break"
    ];
    
    protected $explicitMiddleTag = [
        "else",
        "default"
    ];

    /**
     * conditional looping
     *
     * @var array
     */
    protected $conditionalLooping = [
        "forelse", 
        "empty",
        "endforelse"
    ];

    /**
     * compile syntax
     *
     * @param string $content
     * @return void
     */
    protected function compileStatements($content)
    {
        $pattern = "/@(@?\w+)(\s*)(\( ( (?>[^()]+) | (?3) )* \))?/x";
        $callback = function($matches) {
            // print_r($matches);
            if ( in_array($matches[1], $this->explicitOpenTag) ) {
                return "<?php $matches[1] $matches[3] : ?>";
            }

            elseif ( in_array($matches[1], $this->explicitCloseTag) ) {
                return "<?php $matches[1]; ?>" . $matches[2];
            } 
            
            elseif ( in_array($matches[1], $this->explicitMiddleTag) ) {
                return "<?php $matches[1] : ?>" . $matches[2];
            } 
            
            else {
                return $matches[0];
            }
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * conditional looping
     *
     * @param string $content
     * @return void
     */
    protected function compileConditionalLooping($content)
    {
        $pattern = "/@(@?\w+)(\s*)(\( ( (?>[^()]+) | (?3) )* \))?/x";
        $callback = function($matches) {
            if ( in_array($matches[1], $this->conditionalLooping) && isset($matches[3]) ) {
                preg_match("/\(((.*?)\s*as+\s*(.*?))\)/x", $matches[3], $match);

                return "
                    <?php if (isset($match[2]) && !empty($match[2])) : ?>
                    <?php foreach ($match[1]) : ?>
                ";
            } 

            elseif ( in_array($matches[1], $this->conditionalLooping) && strpos($matches[1], "end") === false ) {
                return "
                    <?php endforeach; ?>
                    <?php else : ?>
                ";
            }
            
            elseif ( in_array($matches[1], $this->conditionalLooping) && strpos($matches[1], "end") !== false ) {
                return "
                    <?php endif; ?>
                ";
            }
            
            else {
                return $matches[0];
            }
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * compile comment
     *
     * @param string $content
     * @return void
     */
    protected function CommentCompiler($content)
    {
        $pattern = sprintf("/%s\s*(.*?)\s*%s/s", "<!--", "-->");
        $callback = function($matches) {
            return "";
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * compile comment
     *
     * @param string $content
     * @return void
     */
    protected function functionViewCompiler($content)
    {
        $pattern = "/@(@?view)(\s*)(\( ( (?>[^()]+) | (?3) )* \))?/x";
        $callback = function($matches) {
            return "<?php \$this->view$matches[3]; ?>";
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

}