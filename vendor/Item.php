<?php
namespace zinux\zg\vendor;

/**
 * Description of Item
 *
 * @author dariush
 */
class Item extends \zinux\zg\baseZg
{
    public $name;
    public $path;
    
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
    }
}
