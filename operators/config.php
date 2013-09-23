<?php
namespace zinux\zg\operators;
/**
 * zg config * handler
 */
class config extends baseOperator
{
    /**
     * ctor a new config handler
     * @param boolean $suppress_header_text check if should it suppress header txt
     */
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $s = $this->GetStatus();
        if(!$s) return;
        if(!isset($s->configs))
        {
            $s->configs = new \stdClass();
            $this->SaveStatus($s);
        }
    }
    /**
     * zg config handler
     * @param type $args
     * @throws \zinux\kernel\exceptions\invalideArgumentException if case of invalid argument supplied
     */
    public function config($args)
    {
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # this can shoud have at least 1 arg
        $this->restrictArgCount($args);
        # get status object
        $s = $this->GetStatus();
        # an array for messages
        $m = array();
        # for all args
        while(count($args))
        {
            # get current arg
            $value = array_shift($args);
            # match with available conf options
            switch($value)
            {
                case "-show-parents":
                    unset($s->configs->show_parents);
                    $m[] = new \zinux\zg\vendor\item("Parents will not show in 'zg status'.", 1);
                    break;
                case "+show-parents":
                    $s->configs->show_parents = 1;
                    $m[] = new \zinux\zg\vendor\item("Parents will show in 'zg status'.", 1);
                    break;
                # if no case this is an invalid arg
                default:
                    throw new \zinux\kernel\exceptions\invalideArgumentException("Undefined config '$value' passed ...");
            }
        }
        # save the status object to file
        $this->SaveStatus($s);
        # foreach generated message
        foreach($m as $value)
        {
            # print it
            if($value->path)
                $this->cout("+ ", 0.5, self::green, 0);
            else
                $this->cout("- ", 0.5, self::red, 0);
            $this->cout($value->name, 0);
        }
    }
    /**
     * zg config show handler
     */
    public function show($args)
    {  
        # this opt is valid under project directories
        if(!$this->CheckZG()) return;
        # no arg expected
        $this->restrictArgCount($args,0,0);
        # invoke a status printer
        $st = new \zinux\zg\operators\status(1);
        # get status object
        $s = $this->GetStatus();
        # only print config part of it
        $st->RecursivePrint($s->configs);
    }
}
