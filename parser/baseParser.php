<?php
namespace zinux\zg\parser;

/**
 * base class for parser
 */
abstract class baseParser extends \zinux\zg\baseZg
{
    /**
     * A command generator instance
     * @var \zinux\zg\commands\commandGenerator
     */
    protected $command_generator;
    /**
     * The passed arg array
     * @var array()
     */
    protected $args;
    /**
     * ctor a new parser
     * @param array $args args
     * @param \zinux\zg\command\baseCommandGenerator $cg A command generator handler
     */
    public function __construct($args, \zinux\zg\command\baseCommandGenerator $cg)
    {
        $this->args = $args;
        $this->command_generator = $cg;
    }
    /**
     * Parser runner
     */
    public abstract function Run();
}

?>
