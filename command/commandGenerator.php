<?php
namespace zinux\zg\command;

/**
 * Description of generateCommands
 *
 * @author dariush
 */
class commandGenerator extends \zinux\zg\baseZg
{
    public function Generate()
    {
        $p = \zinux\kernel\utilities\fileSystem::resolve_path(dirname(__FILE__).'/subcommands');
        if(!$p)
            throw new \Exception("No command directory found");
        $commands = "{";
        foreach(array_filter(glob($p."/*.sc"), 'is_file') as $file)
        {
            $file_name = basename($file, ".sc");
            $commands.="\"$file_name\":".file_get_contents($file);
        }
        $commands.="}";
        $commands = '{
    "repositories": {
        {
            "type": "pear",
            "url": "http://pear2.php.net"
        }
    },
    "require": {
        "pear-pear2/PEAR2_Text_Markdown": "*",
        "pear-pear2/PEAR2_HTTP_Request": "*"
    }
}';
        echo $commands;
        echo "JSON";
        \zinux\kernel\utilities\debug::_var(json_decode($commands));
        
    }
}