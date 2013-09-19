<?php
namespace zinux\zg\vendor;
/**
 * Description of createModuleBoostrap
 *
 * @author dariush
 */
class moduleBoostrap extends \zinux\zg\baseZg
{
    public function __construct(Item $module, Item $moduleBootstrap, $project_path = ".")
    {
        $mbc = "<?php
namespace modules\\{$module->name};

class {$moduleBootstrap->name}
{
    /**
     * A pre-dispatch function
     * @param \\zinux\\kernel\\routing\\request\\\$request
     */
    public function pre_CHECK(\\zinux\\kernel\\routing\\request\\\$request)
    {
    }
    /**
     * A post-dispatch function
     * @param \\zinux\\kernel\\routing\\request\\\$request
     */
    public function post_CHECK(\\zinux\\kernel\\routing\\request\\\$request)
    {
    }
}";
        file_put_contents($moduleBootstrap->path, $mbc);
        $this->cout("+", 1, self::green);
        $s = $this->GetStatus($project_path);
        $s->boostraps->modules[] = $moduleBootstrap;
        $this->SaveStatus($s);
    }
}

?>
