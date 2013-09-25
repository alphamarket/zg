<?php
namespace zinux\zg;    
require_once 'baseZg.php';

# a fail safe for old versions
if(!defined("ZINUX_BUILD_VERSION"))
    die("No zinux version has not defined, It is maybe an old version of zinux, Pleas update your zinux framework from https://github.com/dariushha/zinux");

# check for zinux version
if(version_compare(ZINUX_BUILD_VERSION, ZG_BUILD_ZINUX_VERSION, "<"))
{
    die("The minimal Zinux version required is '".ZG_BUILD_ZINUX_VERSION."'!".PHP_EOL."Your Zinux version is: ".ZINUX_BUILD_VERSION);
}
/**
 * The Zinux Generator Main-Gate
 */
class zg extends \zinux\zg\baseZg
{
    /**
     * provided arguments
     * @var array()
     */
    protected $args;
    /**
     * Executes the zinux generator 
     * @param array $argv argument passed to
     */
    public static function Execute($argv)
    {
        system('clear');
        ob_start();
        $zg = null;
        try
        {            
            \zinux\kernel\caching\fileCache::RegisterCachePath(PRG_CACHE_PATH);
            
            $zg = new zg($argv);
            
            $zg->Run();
        }
        catch(\Exception $e)
        {
            $s = "$1".str_repeat(" ", 4);
            $zg ->cout("[ Error occured ]",0,self::red)
                  ->cout(preg_replace(array("#(<br\s*(/)?>)#i", "#(\n)#i"), array($s, $s), $e->getMessage()), 1, self::yellow);
            if(RUNNING_ENV=="DEVELOPMENT")
            {
                $zg   ->cout(str_repeat("=", 60))
                        ->cout(preg_replace("/([#]\d+)/i", "$1", $e->getTraceAsString()));
            }
        }
            $zg->cout()->cout("[ DONE ]", 0, self::defColor, 0);
            $console_cont = preg_replace(array("#<br\s*(/)?>#i", "#<(/)?pre>#i"),array(PHP_EOL, ""),ob_get_contents()."<br />");
        ob_end_clean();
        echo $console_cont;
    }
    /**
     * ctor a new zinux generator
     * @param array $argv the zg's arguments
     */
    public function __construct($argv)
    {
        if(count($argv) && $argv[0] == $_SERVER['SCRIPT_NAME'])
            array_shift($argv);
        
        $this->args = $argv;
    }
    /**
     * Runs the zinux generator
     */
    public function  Run()
    {
        # create a parser instance
        $parser = new \zinux\zg\parser\parser($this->args, new \zinux\zg\command\commandGenerator());
        # run the parser instance
        $parser->Run();
    }
    /**
     * Cleans up everything
     */
    public function __destruct()
    {
        if(!$this->GetStatus())
            exec("rm -fr ".WORK_ROOT.PRG_CONF_DIRNAME);
    }
}

/**
 * Execute the Zinux Generator
 */
zg::Execute($argv);