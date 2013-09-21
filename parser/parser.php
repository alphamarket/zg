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
            $this->args[] = "-h";
        
        $current_parsing = $this->getOperator();
        
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
    
    public function getOperator($collection = NULL, $explore_arraies = 0)
    {
        if(!$collection)
            $collection = $this->command_generator->Generate();
        elseif(!$this->is_iterable($collection))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Produced argument is not iterable!");
        
        # a fail safe for head keys used command files
        $pre_key_words = array(
                '#\btitle\b#i' => "@title",
                '#\balias\b#i' => "@alias",
                '#\binstance\b#i' => "@instance", 
                '#\bhelp\b#i' => "@help",
                '#\bdefaults\b#i' => "@defaults",
                '#\boptions\b#i' => "@options"
        );
        $post_key_words = array(
                '@title' => "title",
                '@alias' => "alias",
                '@instance' => "instance", 
                '@help' => "help",
                '@options' => "options"
        );
        $args = $this->args = preg_replace(array_keys($pre_key_words), array_values($pre_key_words), $this->args);
        $this->parsed_string = "";
        $current_parsing = $collection;
        while($current_parsing)
        {
            if(!count($this->args))
                goto __EXECUTE;
            $arg = strtolower($this->args[0]);
            if(!isset($current_parsing->{$arg}))
            {
                if($explore_arraies && is_array($current_parsing) && isset($current_parsing[$arg]))
                    goto __NEXT_ROUND;
                
                foreach($current_parsing as $key => $value)
                {
                    if(isset($value->alias) && strtolower($value->alias) == $arg)
                    {
                        $arg = $key;
                        goto __NEXT_ROUND;
                    }
                }
                if(isset($current_parsing->instance))
                    goto __EXECUTE;
                goto __ERROR;
            }
__NEXT_ROUND:
            if(isset($current_parsing->{$arg}))
                $current_parsing = $current_parsing->{$arg};
            elseif($explore_arraies && is_array($current_parsing) && isset($current_parsing[$arg]))
                $current_parsing = $current_parsing[$arg];
            else
                goto __ERROR;
__NEXT_ARG:
            $this->parsed_string.=((strlen($this->parsed_string)?" ":"").array_shift($this->args));
        }
__ERROR:
        $this->args = str_replace(array_keys($post_key_words), array_values($post_key_words), $this->args);
        throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid command '{$this->parsed_string} {$this->args[0]}' in '".implode(" ", $args)."'<br />    Try zg -h.");
__EXECUTE:
        $this->args = str_replace(array_keys($post_key_words), array_values($post_key_words), $this->args);
        return $current_parsing;
    }
}