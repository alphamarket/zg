<?php
namespace zinux\zg\vendor;
/**
 * A container for containing zinux items
 */
class item extends \zinux\zg\baseZg
{
    /**
     * item's name
     * @var string
     */
    public $name;
    /**
     * item's path
     * @var string
     */
    public $path;
    /**
     * item's parent item
     * @var item
     */
    public $parent;
    /**
     * ctor a new item
     * @param string $name item's name
     * @param string $path item's path
     * @param \zinux\zg\vendor\item $parent item's parent item
     */
    public function __construct($name, $path, item &$parent = NULL)
    {
        $this->name = $name;
        $this->path = $path;
        $this->parent = $parent;
        $this->time = date("M-d-Y H:i:s");
    }
}
