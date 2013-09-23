<?php
namespace zinux\zg;

if(!defined("ZG_ROOT"))
{
    ini_set('output_buffering','on');
        
    defined("ZG_ROOT") ||  define("ZG_ROOT", dirname(__FILE__));

    defined("Z_CACHE_ROOT") ||  define("Z_CACHE_ROOT", dirname(dirname(ZG_ROOT))."/zinux");

    defined("PRG_CONF_DIRNAME") || define("PRG_CONF_DIRNAME",".zg");

    defined("PRG_CONF_PATH") || define("PRG_CONF_PATH","/".PRG_CONF_DIRNAME."/");

    defined("PRG_CONF_NAME") || define("PRG_CONF_NAME",".cfg");

    defined("ZG_VERSION") || define("ZG_VERSION","1.4.24");

    defined("RUNNING_ENV") || define("RUNNING_ENV","PRODUCTION");

    //defined("RUNNING_ENV") || define("RUNNING_ENV","DEVELOPMENT");

    /**
     * Trying to locate a zinux project either under CWD
     * or the parent folders
     */
    $d=explode(DIRECTORY_SEPARATOR, getcwd());
    $d = array_filter($d);
    if(preg_match("#[L|U]inux#i", PHP_OS))
        $d = array_merge(array("/"), $d);
    goto __LAUNCH;
__NO_PRG_ERROR:
    die("\n\033[31m>    No project found ....\n");
__LAUNCH:
    while(!count(glob(PRG_CONF_DIRNAME."/", GLOB_ONLYDIR)))
    {
        if(!chdir(".."))
            goto __NO_PRG_ERROR;
        array_pop($d);
        if(!count($d))
            goto __NO_PRG_ERROR;
    }
    unset($d);
    
    defined("WORK_ROOT") || define("WORK_ROOT", getcwd());
}
require_once dirname(__FILE__)."/../baseZinux.php";

defined("ZG_TEMPLE_ROOT") ||  define("ZG_TEMPLE_ROOT", \zinux\kernel\utilities\fileSystem::resolve_path(ZG_ROOT."/resources/templates"));
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
    
    public function cout($content = "<br />", $tap_index = 0, $color = self::defColor, $auto_break = 1)
    {
        ob_start();
            echo str_repeat(" ", $tap_index*4);

             echo $color.$content.self::defColor;

             if($content != "<br />" && $auto_break)
                 echo "<br />";
        echo preg_replace(array("#<br\s*(/)?>#i", "#<(/)?pre>#i"),array(PHP_EOL, ""), ob_get_clean());
        ob_flush();
        return $this;
    }
    public function GetStatus($path = ".")
    {
        $s = NULL;
        if(file_exists("$path".PRG_CONF_PATH.PRG_CONF_NAME))
            $s =  unserialize(file_get_contents("$path".PRG_CONF_PATH.PRG_CONF_NAME));
        if(!$s)
            return $s;
        if(!isset($s->hs))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Hash-sum attrib is missing ....");
        $hs = $s->hs;
        unset($s->hs);
        if($hs != \zinux\kernel\security\hash::Generate(serialize($s),1,1))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Hash-sum mis-matched ....");
        return $s;
    }
    
    public function CreateStatusFile($project_name)
    {
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("./$project_name"))
            mkdir($project_name, 0775);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("./$project_name".PRG_CONF_PATH))
            mkdir("./$project_name".PRG_CONF_PATH);
        
        $s = new \zinux\zg\vendor\status;
        $parent = new vendor\item(basename(realpath(".")), realpath("."));
        $s->project = new vendor\Item("project", realpath("./$project_name/"),$parent);
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        return file_put_contents("./$project_name".PRG_CONF_PATH.PRG_CONF_NAME, serialize($s), LOCK_EX);
    }
    
    public function SaveStatus(\zinux\zg\vendor\status $s)
    {
        unset($s->hs);
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        return file_put_contents("{$s->project->path}/".PRG_CONF_PATH.PRG_CONF_NAME, serialize($s), LOCK_EX);
    }
    public function CheckZG($path = ".", $throw_exception = 0)
    {
        if(!$this->GetStatus($path))
        {
            if($throw_exception)
                throw new \zinux\kernel\exceptions\invalideOperationException("No project have found ...");
            else
            {
                $this ->cout("No project have found ...", 0, self::yellow)
                        ->cout("Try to run 'zg build'!", 0, self::green)
                        ->cout("[ Aborting ]", 0, self::red);
                return false;
            }
        }
        return true;
    }
    public function restrictArgCount($args, $max = 100000, $min = -1)
    {
        if(count($args) > $max)
            throw new \zinux\kernel\exceptions\invalideArgumentException("Too much argument ...");
        if(!$max) return;
        if(($min<0 && !count($args)) || (!(count($args) >= $min)))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Empty argument passed ...");  
    }
    public function is_iterable($var) 
    {
        return (is_array($var) || $var instanceof \Traversable || $var instanceof \stdClass);
    }  
    public function remove_arg(&$args, $value)
    {
        if(!$this->is_iterable($args))
            return false;
        
        foreach($args as $index => $_value)
        {
            if(strtolower($_value) == strtolower($value))
            {
                unset($args[$index]);
                \zinux\kernel\utilities\_array::array_normalize($args);
                return true;
            }
        }
        return false;
        
    }
    public function has_arg($args, $value)
    {
        if(!$this->is_iterable($args))
            return false;
        
        foreach($args as $_value)
        {
            if(strtolower($_value) == strtolower($value))
                return true;
        }
        
        return false;
    }
    public function get_pair_arg_value(&$args, $target_arg, $auto_remove = 0)
    {
        if(!$this->is_iterable($args))
            return NULL;
        $found = array(0, 0);
        foreach($args as $index => $value)
        {
            if($found[0])
            {
                if($auto_remove)
                {
                    unset($args[$index]);
                    unset($args[$found[1]]);
                    \zinux\kernel\utilities\_array::array_normalize($args);
                }
                return $value;
            }
            if(strtolower($value) == strtolower($target_arg))
            {
                $found = array(1, $index);
            }
        }
        return NULL;
    }
    public function inverse_preg_quote($str, $delimiter = NULL)
    {
        $ar = array(
            '\\.'  => '.',
            '\\\\' => '\\',
            '\\+'  => '+',
            '\\*'  => '*',
            '\\?'  => '?',
            '\\['  => '[',
            '\\^'  => '^',
            '\\]'  => ']',
            '\\$'  => '$',
            '\\('  => '(',
            '\\)'  => ')',
            '\\{'  => '{',
            '\\}'  => '}',
            '\\='  => '=',
            '\\!'  => '!',
            '\\<'  => '<',
            '\\>'  => '>',
            '\\|'  => '|',
            '\\:'  => ':',
            '\\-'  => '-'
        );
        if($delimiter)
            $ar["\\$delimiter"] = $delimiter;
        return strtr($str, $ar);
    }
    public function convert_to_relative_path($path, $project_dir = ".", \zinux\zg\vendor\status $s = null)
    {
        if(!$s)
            $s = $this->GetStatus($project_dir);
        if(!$s)
            $this->CheckZG(1);
        if(is_file($path) && false)
            $path = dirname($path);
        $path = preg_replace(
            array("#^".DIRECTORY_SEPARATOR."#i","#(\w+)(".DIRECTORY_SEPARATOR.")#i","#[.]\w+$#i"),
            array("", "$1\\", ""), 
            str_replace($s->project->path, "", dirname($path))
        );
        return $path;
    }
    
    public function check_php_syntax($file_name, $throw_exception_on_error = 1)
    {
        $_file_name = $file_name;
        if(!strlen($file_name) || 
            !($file_name = \zinux\kernel\utilities\fileSystem::resolve_path($file_name, 1)))
            throw new \zinux\kernel\exceptions\notFoundException("'$_file_name' not found...");
        $ret = 0;
        ob_start();
            system( "php -l $file_name 2>&1", $ret);
        $output = ob_get_clean();
        if( $ret !== 0 )
        {
            $matches = array();
            if(preg_match_all( "/Errors\s+parsing\s+".preg_quote($file_name, "/")."/i", $output, $matches) && $throw_exception_on_error)    
            {
                $this->cout("Error parsing '$file_name'",0,self::hiRed);
                throw new \zinux\kernel\exceptions\invalideOperationException($output);
            }
            return false;
        }
        return true;
    }
}