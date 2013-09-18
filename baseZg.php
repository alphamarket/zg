<?php
namespace zinux\zg;
require_once dirname(__FILE__)."/../baseZinux.php";
defined("ZG_ROOT") ||  define("ZG_ROOT", dirname(__FILE__));
defined("WORK_ROOT") ||  define("WORK_ROOT", getcwd());
defined("Z_CACHE_ROOT") ||  define("Z_CACHE_ROOT", dirname(dirname(ZG_ROOT))."/zinux.cache");
defined("RUNNING_ENV") ||  define("RUNNING_ENV", "DEVELOPMENT");

/**
 * Description of baseZg
 *
 * @author dariush
 */
abstract class baseZg extends \zinux\baseZinux
{
    const defColor = "\033[m";
    // blacks
    const black = "\033[30m";
    const hiBlack = "\033[1;30m";
    const bgBlack = "\033[40m";
    // reds
    const red = "\033[31m";
    const hiRed = "\033[1;31m";
    const bgRed = "\033[41m";
    // greens
    const green = "\033[32m";
    const hiGreen = "\033[1;32m";
    const bgGreen = "\033[42m";
    // yellows
    const yellow = "\033[33m";
    const hiYellow = "\033[1;33m";
    const bgYellow = "\033[43m";
    // blues
    const blue = "\033[34m";
    const hiBlue = "\033[1;34m";
    const bgBlue = "\033[44m";
    // magentas
    const magenta = "\033[35m";
    const hiMagenta = "\033[1;35m";
    const bgMagenta = "\033[45m";
    // cyans
    const cyan = "\033[36m";
    const hiCyan = "\033[1;36m";
    const bgCyan = "\033[46m";
    // whites
    const white = "\033[37m";
    const hiWhite = "\033[1;37m";
    const bgWhite = "\033[47m";
            
    public function Initiate(){}
    
    public function cout($content = "<br />", $tag_index = 0, $color = self::defColor, $auto_break = 1)
    {
        while($tag_index--)
            echo "    ";
        
        echo $color.$content.self::defColor;
        
        if($content != "<br />" && $auto_break)
            echo "<br />";
        
        return $this;
    }
}