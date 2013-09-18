<?php
namespace zinux\zg\parser;
/**
 * Description of parser
 *
 * @author dariush
 */
class parser extends baseParser
{    
    public function Run()
    {
        if(!count($this->args))
            $this->args[] = "help";
        if($this->args[0]=="help")
        {
            array_shift($this->args);
            $help  = new helpParser($this->args, $this->command_generator);
            $help->Run();
            return;
        }
        if(count($this->args)<2 || ($this->args[0] != "new" || $this->args[1] != "project"))
            if(!$this->CheckZG(0))
            {
                $this ->cout("No project have found ...", 0, self::yellow)
                        ->cout("[ Aborting ]", 0, self::red);
                return;
            }
        
        $parsed_string = "zg";
        $current_parsing = $this->command_generator->Generate();
        while($current_parsing)
        {
            if(!count($this->args))
                goto __EXECUTE;
            if(!isset($current_parsing->{$this->args[0]}))
            {
                if(isset($current_parsing->instance))
                    goto __EXECUTE;
                goto __ERROR;
            }
            $current_parsing = $current_parsing->{$this->args[0]};
            $parsed_string.=(" ".array_shift($this->args));
        }
__ERROR:
        throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid command '".self::yellow."$parsed_string ".implode(" ", $this->args).self::defColor."'");
__EXECUTE:
        if(!isset($current_parsing->instance->class) || !isset($current_parsing->instance->method))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("The $parsed_string metadata structure is mis-configured, target action's {class} or {method} has not been specified ...");
        $c = $current_parsing->instance->class;
        $c = new $c;
        $rf = new \ReflectionClass($c);
        if(!$rf ->isSubclassOf("\\zinux\\zg\\resources\\operator\\baseOperator"))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("Target class {$rf->getName()} is not subclass of '\\zinux\\zg\\resources\\operator\\baseOperator'");
        if(!method_exists($c, $current_parsing->instance->method))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("Method '{$current_parsing->instance->method}' does not exists in '{$current_parsing->instance->class}'");
        # execute the target operation's action
        $c->{$current_parsing->instance->method}($this->args);
    }
}