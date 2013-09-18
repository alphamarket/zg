<?php
namespace zinux\zg\parser;

/**
 * Description of baseParser
 *
 * @author dariush
 */
abstract class baseParser extends \zinux\zg\baseZg
{
    /**
     *
     * @var \zinux\zg\commands\commandGenerator
     */
    protected $command_generator;
    /**
     *
     * @var array()
     */
    protected $args;
    
    public function __construct($args, \zinux\zg\command\baseCommandGenerator $cg)
    {
        $this->args = $args;
        $this->command_generator = $cg;
    }
    
    public abstract function Run();
}

?>
