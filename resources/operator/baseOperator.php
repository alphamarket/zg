<?php
namespace zinux\zg\resources\operator;

abstract class baseOperator extends \zinux\zg\baseZg
{
    public function Run($opt)
    {
        $s = $this->GetStatus();
        $this->cout("", 1,self::defColor, 0);
        foreach($opt as $value)
        {
            system($value);
            if($s)
            {
                $h = new \stdClass();
                $h->opt = $value;
                $h->time = date("M-d-Y H:i:s");
                $s->history[] = $h;
            }
            $this->cout("+", 0, self::green, 0);
        }
        if($s)
            $this->SaveStatus($s);
        
        $this ->cout()
                ->cout("[ DONE ]", 0, self::yellow);
    }
    public function GetStatus()
    {
        if(file_exists("./.zg.cfg"))
            return unserialize(file_get_contents("./.zg.cfg"));
        return null;
    }
    public function CreateStatusFile($project_name)
    {
        $s = new \zinux\zg\vendor\status;
        $s->project_name = $project_name;
        return file_put_contents("./$project_name/.zg.cfg", serialize($s), LOCK_EX);
    }
    public function SaveStatus(\zinux\zg\vendor\status $s)
    {
        return file_put_contents("./.zg.cfg", serialize($s), LOCK_EX);
    }
    
    public function CheckProject()
    {
        if(!$this->GetStatus())
            throw new \zinux\kernel\exceptions\invalideOperationException("The project file not found ....");
    }
}
