<?php
namespace zg\command;

/**
 * Description of generateCommands
 *
 * @author dariush
 */
class baseCommandGenerator extends \zg\baseZg
{
    /**
     * command source folder address 
     * @var string
     */
    protected $path;
    /**
     * Construct a command generator
     * @param string $path commands' folder address
     * @throws \zinux\kernel\exceptions\notFoundException in case of command folder does not exist
     */
    public function __construct($path = COMMANDS_ROOT)
    {
        $this->path = \zinux\kernel\utilities\fileSystem::resolve_path($path);
        if(!$this->path)
            throw new \zinux\kernel\exceptions\notFoundException("No command directory found");
    }
}