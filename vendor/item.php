<?php
namespace zinux\zg\vendor;
/**
 * Description of createModuleBoostrap
 *
 * @author dariush
 */
class item extends \zinux\zg\baseZg
{
    public $name;
    public $path;
    
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
        $this->time = date("M-d-Y H:i:s");
    }
}
