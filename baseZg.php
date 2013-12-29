<?php
namespace zinux\zg;

/**
 * Some definitions here
 */
if(!defined("ZG_ROOT"))
{
    # turning on output buffering
    ini_set('output_buffering','on');
    # define zinux build version
    defined("ZG_BUILD_ZINUX_VERSION") || define("ZG_BUILD_ZINUX_VERSION", "3.0.0");
    # defines zinux's root dir
    defined("ZG_ROOT") ||  define("ZG_ROOT", dirname(__FILE__));
    # defines zinux cache dir
    defined("Z_CACHE_ROOT") ||  define("Z_CACHE_ROOT", dirname(dirname(ZG_ROOT))."/zinux");
    # defines client APP conf's dir name
    defined("PRG_CONF_DIRNAME") || define("PRG_CONF_DIRNAME",".zg");
    # defines client APP conf path
    defined("PRG_CONF_PATH") || define("PRG_CONF_PATH","/".PRG_CONF_DIRNAME."/");
    # defines client APP conf name
    defined("PRG_CONF_NAME") || define("PRG_CONF_NAME",".cfg");
    # defines default command files' root
    defined("COMMANDS_ROOT") || define("COMMANDS_ROOT", ZG_ROOT.'/resources/commands');
    # defines ZG's version
    defined("ZG_VERSION") || define("ZG_VERSION","1.6.2");
    # defines running environment
    defined("RUNNING_ENV") || define("RUNNING_ENV","PRODUCTION");
    # an other alternative running environment definition
    //defined("RUNNING_ENV") || define("RUNNING_ENV","DEVELOPMENT");
    # if we are in production mode
    if(RUNNING_ENV == "PRODUCTION")
    {
        # turn off the error display
        ini_set('display_errors','off');
    }
    # get CWDs
    $cwd  = getcwd();
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
    goto __SKIP_ERROR;
    die("\n\033[31m>    No project found ....\n");
__SKIP_ERROR:
    chdir($cwd);
    goto __ZG;
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
__ZG:
    # defines ultimate CWD
    defined("WORK_ROOT") || define("WORK_ROOT", getcwd()."/");
    # defined client APP cache dir
    defined("PRG_CACHE_PATH") || define("PRG_CACHE_PATH", WORK_ROOT.PRG_CONF_PATH."cache");
}
require_once dirname(__FILE__)."/../baseZinux.php";
# defines ZG templates root
defined("ZG_TEMPLE_ROOT") ||  define("ZG_TEMPLE_ROOT", \zinux\kernel\utilities\fileSystem::resolve_path(ZG_ROOT."/resources/templates"));
/**
 * baseZg class
 *      All ZG classes with inherit from this
 */
abstract class baseZg extends \zinux\baseZinux
{
    # default colot
    const defColor = "\033[m";
    # blacks
    const black = "\033[30m";
    const hiBlack = "\033[1;30m";
    const bgBlack = "\033[40m";
    # reds
    const red = "\033[31m";
    const hiRed = "\033[1;31m";
    const bgRed = "\033[41m";
    # greens
    const green = "\033[32m";
    const hiGreen = "\033[1;32m";
    const bgGreen = "\033[42m";
    # yellows
    const yellow = "\033[33m";
    const hiYellow = "\033[1;33m";
    const bgYellow = "\033[43m";
    # blues
    const blue = "\033[34m";
    const hiBlue = "\033[1;34m";
    const bgBlue = "\033[44m";
    # magentas
    const magenta = "\033[35m";
    const hiMagenta = "\033[1;35m";
    const bgMagenta = "\033[45m";
    # cyans
    const cyan = "\033[36m";
    const hiCyan = "\033[1;36m";
    const bgCyan = "\033[46m";
    # whites
    const white = "\033[37m";
    const hiWhite = "\033[1;37m";
    const bgWhite = "\033[47m";

    public function Initiate(){}
    /**
     * std cout function
     * @param string $content target text to be printed
     * @param float $tap_index tab count before outputing the content
     * @param string $color the text color some CONST defined in 'self::' you can use it
     * @param boolean $auto_break if should auto break line after couting
     * @return \zinux\zg\baseZg $this
     */
    public function cout($content = "<br />", $tap_index = 0, $color = self::defColor, $auto_break = 1)
    {
        ob_start();
            echo str_repeat(" ", $tap_index*4);

             echo $this->getColor($color).$content.$this->getColor();

             if($content != "<br />" && $auto_break)
                 echo "<br />";
        echo preg_replace(array("#<br\s*(/)?>#i", "#<(/)?pre>#i"),array(PHP_EOL, ""), ob_get_clean());
        ob_flush();
        return $this;
    }
    /**
     * gets status object from status file
     * @param string $project_path project directory
     * @return vendors\status target saved status object
     * @throws \zinux\kernel\exceptions\invalideArgumentException if status object's hash-sum mis-matched
     */
    public function GetStatus($project_path = ".")
    {
        $s = NULL;
        if(file_exists("$project_path".PRG_CONF_PATH.PRG_CONF_NAME))
            $s =  unserialize(file_get_contents("$project_path".PRG_CONF_PATH.PRG_CONF_NAME));
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
    /**
     * creates new status file
     * it also creates project directory if not exists
     * @param string $project_name project name
     */
    public function CreateStatusFile($project_name)
    {
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("./$project_name"))
            mkdir($project_name, 0775);
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("./$project_name".PRG_CONF_PATH))
            mkdir("./$project_name".PRG_CONF_PATH);

        $s = new \zinux\zg\vendors\status;
        $parent = new vendors\item(basename(realpath(".")), realpath("."));
        $s->project = new vendors\Item("project", realpath("./$project_name/"),$parent);
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        file_put_contents("./$project_name".PRG_CONF_PATH.PRG_CONF_NAME, serialize($s), LOCK_EX);
    }
    /**
     * saves status object into config file
     * @param \zinux\zg\vendors\status $s the status object
     */
    public function SaveStatus(\zinux\zg\vendors\status $s)
    {
        unset($s->hs);
        $s->hs = \zinux\kernel\security\hash::Generate(serialize($s),1,1);
        file_put_contents("{$s->project->path}/".PRG_CONF_PATH.PRG_CONF_NAME, serialize($s), LOCK_EX);
    }
    /**
     * checks if zg config file exists in given project path
     * @param string $project_path target project directory
     * @param boolean $throw_exception in case of non-existance config file, should it throw an exception or return a FALSE
     * @return boolean TRUE if config file exists otherwise FALSE
     * @throws \zinux\kernel\exceptions\invalideOperationException if $throw_exception enabled and config file does not exist
     */
    public function CheckZG($project_path = ".", $throw_exception = 0)
    {
        if(!$this->GetStatus($project_path))
        {
            if($throw_exception)
                throw new \zinux\kernel\exceptions\invalideOperationException("No project have found ...");
            else
            {
                $this ->cout("No project have found ...", 0, self::getColor(self::yellow))
                        ->cout("Try to run 'zg build'!", 0, self::getColor(self::green))
                        ->cout("[ Aborting ]", 0, self::getColor(self::red));
                return false;
            }
        }
        return true;
    }
    /**
     * but an restriction on a given array
     * @param array $args the target array
     * @param int $max maximun count of array's items
     * @param int $min minimum count of array's items
     * @throws \zinux\kernel\exceptions\invalideArgumentException if restriction not satisfied throws an exception
     */
    public function restrictArgCount($args, $max = 100000, $min = -1)
    {
        if(count($args) > $max)
            throw new \zinux\kernel\exceptions\invalideArgumentException("Too much argument ...");
        if(!$max) return;
        if(($min<0 && !count($args)) || (!(count($args) >= $min)))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Empty argument passed ...");
    }
    /**
     * checks if an given item is an iterable instance
     * @param mixed $var target item to check
     * @return boolean TRUE if its is iterable instance otherwise FALSE
     */
    public function is_iterable($var)
    {
        return (is_array($var) || $var instanceof \Traversable || $var instanceof \stdClass);
    }
    /**
     * removed an value from an iterable item
     * @param mixed $args target item to search into
     * @param string $value target value to delete
     * @param boolean $remove_all should remove all matching items or only the first
     * @return boolean returns true if any item removed otherwise returns false
     */
    public function remove_arg(&$args, $value, $remove_all = 0)
    {
        if(!$this->is_iterable($args))
            return false;
        $found = 0;
        foreach($args as $index => $_value)
        {
            if(($found = (strtolower($_value) == strtolower($value))))
            {
                unset($args[$index]);
                \zinux\kernel\utilities\_array::array_normalize($args);
                if($remove_all)
                    return true;
            }
        }
        return $found;
    }
    /**
     * checks if an value exists in an iterable item
     * @param mixed $args target item
     * @param string $value target value string
     * @return boolean TRUE if $args has the $value otherwise FALSE
     */
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
    /**
     * gets pair value of an key-value item
     * @param mixed $args an iterable item
     * @param string $target_arg target key-value
     * @param boolean $auto_remove should auto-remove the item if they exist
     * @return string the key-value's value
     */
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
    /**
     * inverse the preg_quote()'s effects in a string
     * @param string $str target string
     * @param string $delimiter the delimiter user in string
     * @return string inversed string
     */
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
    /**
     * create a relative directory based namespace string
     * from a given path and in perspective of a project directory
     * @return string
     */
    public function convert_to_relative_path($path, $project_dir = ".", \zinux\zg\vendors\status $s = null)
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
    /**
     * check syntax  of a php file
     * @param string $file_name target file name
     * @param boolean $throw_exception_on_error
     * @return TRUE if no syntax error happened, otherwise FALSE
     */
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
                $this->cout("Error parsing '$file_name'",0,self::getColor(self::hiRed));
                throw new \zinux\kernel\exceptions\invalideOperationException($output);
            }
            return false;
        }
        return true;
    }
    /**
     * safe file requirement
     * @param string $file_name target file name
     * @param boolean $silent check if should file's output printed or not
     * @param boolean $once check if it should use 'require_once' instead of 'require'
     */
    public function require_file($file_name, $silent = 1, $once = 1)
    {
        ob_start();
        if($once)
            $f = require_once $file_name;
        else
            $f = require $file_name;
        if($silent)
            ob_end_clean();
        else
            ob_end_flush();
        return $f;
    }
    /**
     * get proper colors string code
     * @param FLAG $color
     * @return string color string code or an empty string in Windows OS
     */
    public static function getColor($color = self::defColor)
    {
        # check if OS is fucking WINDOWS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            # It doesn't support ANSI color standard codes
            # NO COLOR IN WINDOWS
            return "";
        }
        return $color;
    }
}
