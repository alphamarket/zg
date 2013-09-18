<?php
namespace zinux\zg\parser;
/**
 * Description of parser
 *
 * @author dariush
 */
class parser extends \zinux\zg\baseZg
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
    
    public function __construct($args, \zinux\zg\commands\baseCommandGenerator $cg)
    {
        $this->args = $args;
        $this->command_generator = $cg;
    }
    
    public function Run()
    {
        
    }
}