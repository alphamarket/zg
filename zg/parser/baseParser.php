<?php
namespace zg\parser;

/**
 * base class for parser
 */
abstract class baseParser extends \zg\baseZg
{
    /**
     * A command generator instance
     * @var \zg\commands\commandGenerator
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
     * @param \zg\command\baseCommandGenerator $cg A command generator handler
     */
    public function __construct($args, \zg\command\baseCommandGenerator $cg)
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
