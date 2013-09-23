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
        # this can shoud have at l
        $this->restrictArgCount($args);
        $s = $this->GetStatus();
        $m = array();
        while(count($args))
        {
            $value = array_shift($args);
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
                default:
                    throw new \zinux\kernel\exceptions\invalideArgumentException("Undefined config '$value' passed ...");
            }
        }
        $this->SaveStatus($s);
        foreach($m as $value)
        {
            if($value->path)
                $this->cout("+ ", 0.5, self::green, 0);
            else
                $this->cout("- ", 0.5, self::red, 0);
            $this->cout($value->name, 0);
        }
    }
    public function show($args)
    {  
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args,0,0);
        $st = new \zinux\zg\operators\status(1);
        $s = $this->GetStatus();
        $st->RecursivePrint($s->configs);
    }
}
