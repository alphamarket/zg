<?php
namespace zg\command;

/**
 * Generates command objects from json data
 *
 * @author dariush
 */
class commandGenerator extends baseCommandGenerator
{
    /**
     * Fetch commands object from command path
     * @return \stdClass the fetched commands
     * @throws \zinux\kernel\exceptions\invalidOperationException in case of no command object generated
     */
    public function Generate()
    {
        # create a global object brace for all commands
        $commands = "{";
        # foreach json file in directory
        foreach(array_filter(glob($this->path."/*.json"), 'is_file') as $file)
        {
            # if it is no readable 
            if(!is_readable($file))
                # throw an exceptions
                throw new \zinux\kernel\exceptions\accessDeniedException("Cannot read from '$file'.");
            # fetch the command's file name 
            $file_name = basename($file, ".json");
            # create an object with access name same as command file's name
            # but content of the command file as its value
            $commands.="\"$file_name\":".file_get_contents($file).",";
        }
        # close up the global object brace for all commands
        $commands = preg_replace("#(},)?$#i","}", $commands);
        # decode the json string
        $json = json_decode($commands);
        # if no json object created 
        if(!$json)
            # throw an indicator exception, seems files' contents are buggy!
            throw new \zinux\kernel\exceptions\invalidOperationException("Json decoded value is empty");
        # return the json object
        return $json;
    }
}