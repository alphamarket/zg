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
        
        $current_parsing = $this->getOperator($this->args);
        
        if(!isset($current_parsing->instance->class) || !isset($current_parsing->instance->method))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("The {$this->parsed_string} metadata structure is mis-configured, target action's {class} or {method} has not been specified ...");
        
        $c = $current_parsing->instance->class;
        $c = new $c;
        $rf = new \ReflectionClass($c);
        
        if(!$rf ->isSubclassOf("\\zinux\\zg\\resources\\operator\\baseOperator"))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("Target class {$rf->getName()} is not subclass of '\\zinux\\zg\\resources\\operator\\baseOperator'");
        
        if(!method_exists($c, $current_parsing->instance->method) || !is_callable(array($c, $current_parsing->instance->method)))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("Method '{$current_parsing->instance->method}' does not exists or not accessible in '{$current_parsing->instance->class}'");

        # execute the target operation's action
        $c->{$current_parsing->instance->method}($this->args);
    }
    
    public function getOperator()
    {
        # a fail safe for head keys used command files
        $key_words = array(
                "title" => "@title",
                "alias" => "@alias",
                "instance" => "@instance", 
                "help" => "@help",
                "options" => "@options"
        );
        $this->args = str_replace(array_keys($key_words), array_values($key_words), $this->args);
        $this->parsed_string = "zg";
        $current_parsing = $this->command_generator->Generate();
        while($current_parsing)
        {
            if(!count($this->args))
                goto __EXECUTE;
            if(!isset($current_parsing->{$this->args[0]}))
            {
                foreach($current_parsing as $key => $value)
                {
                    if(isset($value->alias) && strtolower($value->alias) == $this->args[0])
                    {
                        $this->args[0] = $key;
                        goto __NEXT_ROUND;
                    }
                }
                if(isset($current_parsing->instance))
                    goto __EXECUTE;
                goto __ERROR;
            }
__NEXT_ROUND:
            $current_parsing = $current_parsing->{$this->args[0]};
__NEXT_ARG:
            $this->parsed_string.=(" ".array_shift($this->args));
        }
__ERROR:
        throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid command '".self::yellow."{$this->parsed_string} ".implode(" ", $this->args).self::defColor."'");
__EXECUTE:
        $this->args = str_replace(array_values($key_words), array_keys($key_words), $this->args);
        return $current_parsing;
    }
}