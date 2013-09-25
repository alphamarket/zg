<?php
namespace zinux\zg\parser;
defined("PARSER_KEYWORDS") || 
define("PARSER_KEYWORDS", 
    serialize(
        array(
            'title' => "title",
            'alias' => "alias",
            'instance' => "instance", 
            'help' => "help",
            'options' => "options",
            "defaults" => "defaults",    
            "notes" => "notes",    
        )
    )
);
/**
 * General command parser
 * The opt provided in this class 
 * will handel any commands type 
 */
class parser extends baseParser
{    
    /**
     * Parser runner
     * @throws \zinux\kernel\exceptions\invalideOperationException in case of data miss-configured
     */
    public function Run()
    {
        # if no args provided 
        if(!count($this->args))
            # print help content
            $this->args[] = "-h";
        # get operator based on passed args
        $current_parsing = $this->getOperator();
        # if it does not match with standard operator data structure
        if(!isset($current_parsing->instance->class) || !isset($current_parsing->instance->method))
            # it means no opt exists for passed args
            throw new \zinux\kernel\exceptions\invalideOperationException
                (self::yellow."No operator found for '".self::cyan.$this->parsed_string.self::yellow."'.");
        # create the opt's related object
        $c = $current_parsing->instance->class;
        $c = new $c;
        $rf = new \ReflectionClass($c);
        # all opt should inherit from baseOperator
        if(!$rf ->isSubclassOf("\\zinux\\zg\\operators\\baseOperator"))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("Target class {$rf->getName()} is not subclass of '\\zinux\\zg\\resources\\operator\\baseOperator'");
        # validate opt's opt method 
        if(!method_exists($c, $current_parsing->instance->method) || !is_callable(array($c, $current_parsing->instance->method)))
            throw new \zinux\kernel\exceptions\invalideOperationException
                ("Method '{$current_parsing->instance->method}' does not exist or not accessible in '{$current_parsing->instance->class}'");
        # execute the target operation's action
        $c->{$current_parsing->instance->method}($this->args);
    }
    /**
     * Fetches proper opt object
     * @param \stdClass $collection the start point to search
     * @param boolean $explore_arraies check if it should explore arraies too
     * @return \stdClass || null
     * @throws \zinux\kernel\exceptions\invalideArgumentException if no opt found or $collection is not iterable
     */
    public function getOperator($collection = NULL, $explore_arraies = 0)
    {
        # if no collection provided
        if(!$collection)
            # use command generator to fetch collection
            $collection = $this->command_generator->Generate();
        # the collection should be an iterable instance
        if(!$this->is_iterable($collection))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Produced argument is not iterable!");
        # a fail safe for keywords used command files
        $pre_key_words = array(
                '#\btitle\b#i' => "@title",
                '#\balias\b#i' => "@alias",
                '#\binstance\b#i' => "@instance", 
                '#\bhelp\b#i' => "@help",
                '#\bdefaults\b#i' => "@defaults",
                '#\boptions\b#i' => "@options",
                "#\bdefaults\b#i" => "@defaults",    
                "#\bnotes\b#i" => "@notes"    
        );
        # post conversion for keywords
        $post_key_words = array(
                '@title' => "title",
                '@alias' => "alias",
                '@instance' => "instance", 
                '@help' => "help",
                '@options' => "options", 
                "@defaults" => "defaults",    
                "@notes" => "notes",    
        );
        # replace any keyword occurrence in args
        $args = $this->args = preg_replace(array_keys($pre_key_words), array_values($pre_key_words), $this->args);
        # holds how far parser proceed in parsing commands
        $this->parsed_string = "";
        # holds current command 
        $current_parsing = $collection;
        # while current command is valid
        while($current_parsing)
        {
            # if no arg has remained
            if(!count($this->args))
                # go for execution of current command
                goto __EXECUTE;
            # normalize the arg
            $arg = strtolower($this->args[0]);
            # if current command does not contain the arg value
            if(!isset($current_parsing->{$arg}))
            {
                # if array exploration is enable and 
                # current command is indeed an array
                # and current command array contains the arg elem
                if($explore_arraies && is_array($current_parsing) && isset($current_parsing[$arg]))
                    # accept it, and proceed to next round
                    goto __NEXT_ROUND;
                # try mathcing aliases with the arg
                foreach($current_parsing as $key => $value)
                {
                    # if any alias hit with the arg
                    if(isset($value->alias) && strtolower($value->alias) == $arg)
                    {
                        # acquire the real name for command
                        $arg = $key;
                        # accept it, and proceed to next round
                        goto __NEXT_ROUND;
                    }
                }
                # it current command contains an operator handler
                if(isset($current_parsing->instance))
                    # suppose the rest of the args as its arg
                    goto __EXECUTE;
                # if none of above cases matches 
                # indicate an error happened
                goto __ERROR;
            }
            # the next round operations
__NEXT_ROUND:
            # if current command contains the arg property
            if(isset($current_parsing->{$arg}))
                # move into it
                $current_parsing = $current_parsing->{$arg};
            # if not, and if array exploration is enable and 
            # current command is indeed an array
            # and current command array contains the arg elem
            elseif($explore_arraies && is_array($current_parsing) && isset($current_parsing[$arg]))
                # move into it
                $current_parsing = $current_parsing[$arg];
            else
                # otherwise indicate an error happened
                goto __ERROR;
__NEXT_ARG:
            # update parsed command
            # this also will shift the $this->args too
            $this->parsed_string.=((strlen($this->parsed_string)?" ":"").array_shift($this->args));
        }
        # error procedures
__ERROR:
        # normalize the possible converted keywords
        $this->args = str_replace(array_keys($post_key_words), array_values($post_key_words), $this->args);
        # throw an exception indicates that invalid command parsed
        throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid command '{$this->parsed_string} {$this->args[0]}' in '".implode(" ", $args)."'<br />    Try zg -h.");
        # command process success operations
__EXECUTE:
        # normalize the possible converted keywords
        $this->args = str_replace(array_keys($post_key_words), array_values($post_key_words), $this->args);
        # return the fetched command
        # the args for command is stored in $this->args
        return $current_parsing;
    }
}