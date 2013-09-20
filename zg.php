<?php
namespace zinux\zg;
require_once 'baseZg.php';
/**
 * Description of zg
 *
 * @author dariush
 */
class zg extends \zinux\zg\baseZg
{
    /**
     * provided arguments
     * @var array()
     */
    protected $args;
    
    public static function Execute($argv)
    {
        system('clear');
        ob_start();
        $zg = null;
        try
        {
            if(!count($argv))
                throw new Exception("No argument supplied ...");
            
            \zinux\kernel\caching\fileCache::RegisterCachePath(WORK_ROOT."/.zg/cache");
            
            $zg = new zg($argv);
            
            if(self::checkCache($zg))
            {
                $zg->Run();
            }
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
            $zg->cout()->cout("[ DONE ]", 0, self::yellow);
            $console_cont = preg_replace(array("#<br\s*(/)?>#i", "#<(/)?pre>#i"),array(PHP_EOL, ""),ob_get_contents()."<br />");
        ob_end_clean();
        echo $console_cont;
    }
    
    public static function checkCache(zg $zg)
    {
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(Z_CACHE_ROOT))
        {
            $zg->cout("Notice: ", 0, self::yellow, 0);
            $zg->cout("zinux's cache directory could not found at '", 0, self::defColor, 0);
            $zg->cout(Z_CACHE_ROOT, 0, self::yellow,0);
            $zg->cout("'");
            $zg->cout("Creating the cache ...", 0, self::hiGreen);
            system("cp -R ".ZINUX_ROOT." ".Z_CACHE_ROOT);
            if(\zinux\kernel\utilities\fileSystem::resolve_path(Z_CACHE_ROOT))
                $zg->cout("Done ...", 0, self::hiGreen);
            else
            {
                $zg->cout("[ Oops!!! ] ".Z_CACHE_ROOT." still does not exist ....", 0, self::red);
                $zg->cout("Check your permissions ...", 0, self::yellow);
                $zg->cout("[ Aborting ]", 0, self::red);
                return false;
            }
        }
        return true;
    }
    
    public function __construct($argv)
    {
        if($argv[0] == $_SERVER['SCRIPT_NAME'])
            array_shift($argv);
        
        $this->args = $argv;
        # normalize the array
        foreach($argv as $key=> $value)
        {   break;
            $this->args[$key] = strtolower($value);
        }
    }
    
    public function  Run()
    {
        # create a parser instance
        $parser = new \zinux\zg\parser\parser($this->args, new \zinux\zg\command\commandGenerator());
        # run the parser instance
        $parser->Run();
    }
}


zg::Execute($argv);