<?php
namespace zinux\zg\vendors\creators;
/**
 * layout creator
 */
class createLayout extends \zinux\zg\baseZg
{
    /**
     * ctor a new layout
     * @param \zinux\zg\vendors\item $module target module item
     * @param \zinux\zg\vendors\item $layout target layout item
     * @param string $project_path project directory
     */
    public function __construct(\zinux\zg\vendors\item $module, \zinux\zg\vendors\item $layout, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($layout->path, $project_path);;
        $this ->cout("Creating new layout '", 0.5,  self::getColor(self::defColor), 0)
                ->cout($layout->name, 0, self::getColor(self::yellow), 0)
                ->cout("' for '",0,self::getColor(self::defColor), 0)
                ->cout($ns, 0, self::getColor(self::yellow), 0)
                ->cout("' module.");
        if(!\zinux\kernel\utilities\fileSystem::resolve_path(dirname($layout->path)))
            mkdir(dirname($layout->path), 0775);
        
        $mbc = "<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <?php \$this->RenderTitle();?>
        <?php \$this->RenderImports(); ?>
    </head>
    <body>
        <?php echo \$this->content; ?>
    </body>
</html>";
        file_put_contents($layout->path, $mbc);
        
        $s = $this->GetStatus($project_path);
        $layout->parent = $module;
        $s->modules->collection[strtolower($module->name)]->layout[strtolower($layout->name)] = $layout;
        $this->SaveStatus($s);
    }
}
