<?php
namespace zinux\zg\command;

/**
 * Description of generateCommands
 *
 * @author dariush
 */
class commandGenerator extends baseCommandGenerator
{
    public function Generate()
    {
        $p = \zinux\kernel\utilities\fileSystem::resolve_path(ZG_ROOT.'/resources/command');
        if(!$p)
            throw new \Exception("No command directory found");
        $commands = "{";
        foreach(array_filter(glob($p."/*.sc"), 'is_file') as $file)
        {
            $file_name = basename($file, ".sc");
            $commands.="\"$file_name\":".file_get_contents($file).",";
        }
        $commands = preg_replace("#,$#i","}", $commands);
        return json_decode($commands);
        
    }
}