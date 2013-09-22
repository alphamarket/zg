<?php
namespace zinux\zg\operators;

class config extends baseOperator
{
    public function __construct($suppress_header_text = 0)
    {
        parent::__construct($suppress_header_text);
        $s = $this->GetStatus();
        if(!isset($s->configs))
            $s->configs = new \stdClass();
        $this->SaveStatus($s);
    }
    public function config($args)
    {
        if(!$this->CheckZG()) return;
        $this->restrictArgCount($args);
        $s = $this->GetStatus();
        $m = array();
        while(count($args))
        {
            $value = array_shift($args);
            switch($value)
            {
                case "-history":
                    $s->configs->skip_history = 1;
                    unset($s->history);
                    $m[] = new \zinux\zg\vendor\item("History recording will skip.", 1);
                    $m[] = new \zinux\zg\vendor\item("History records deleted.", 0);
                    break;
                case "+history":
                    unset($s->configs->skip_history);
                    $m[] = new \zinux\zg\vendor\item("Histories will record.", 1);
                    break;
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
                $this->cout("+ ", 1, self::green, 0);
            else
                $this->cout("- ", 1, self::red, 0);
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
