<?php
require_once 'baseZg.php';
/**
 * Description of zg
 *
 * @author dariush
 */
class zg extends zinux\zg\baseZg
{
    /**
     * provided arguments
     * @var array()
     */
    protected $args;
    
    public static function Execure($argv)
    {
        new zg($argv);
    }
    
    public function __construct($argv)
    {
        if($argv[0] == $_SERVER['SCRIPT_NAME'])
            array_shift($argv);
        # normalize the array
        foreach($argv as $key=> $value)
        {
            $this->args[$key] = strtolower($value);
        }
        # create a parser instance
        $parser = new \zinux\zg\parser\parser($this->args, new zinux\zg\command\commandGenerator());
        # run the parser instance
        $parser->Run();
    }
}
system('clear');
ob_start();
try
{
    if(!count($argv))
        throw new Exception("No argument supplied ...");
    
    \zinux\kernel\caching\fileCache::RegisterCachePath(ZG_ROOT."/bin/cache");
    /**
     * Execute the zinux generator
     */
    zg::Execure($argv);
}
catch(Exception $e)
{
    echo "<br />Error occured ...<br />";
    echo $e->getMessage()."<br />";
    if(RUNNING_ENV=="DEVELOPMENT")
    {
        echo$e->getTraceAsString();
    }
}
    $console_cont = preg_replace(array("#<br\s*(/)?>#i", "#<(/)?pre>#i"),array(PHP_EOL, ""),ob_get_contents());
ob_end_clean();
echo $console_cont;