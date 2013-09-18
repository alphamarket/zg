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
        # getting ride of $HOME/.zinux/zinux/zg/bin/../zg.php arg.
        unset($argv[0]);
        $this->args = $argv;
        # normalize args array
        \zinux\kernel\utilities\_array::array_normalize($this->args);
        # create a parser instance
        $parser = new \zinux\zg\parser\parser($this->args, new zinux\zg\commands\commandGenerator());
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
    
    \zinux\kernel\caching\fileCache::RegisterCachePath(getcwd()."/bin/cache");
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