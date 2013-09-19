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
    public $parent;
    
    public function __construct($name, $path, item &$parent = NULL)
    {
        $this->name = $name;
        $this->path = $path;
        $this->parent = $parent;
        $this->time = date("M-d-Y H:i:s");
    }
}
