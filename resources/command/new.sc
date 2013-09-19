{
        "alias":"n",
        "project":
        {
            "title":"Create new project",
            "alias":"p",
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"project"
            },
            "help":
            {
                "command":"zf new project $project_name",
                "alias": "zf n p $project_name",
                "detail":"Creates a new zinux project"
            }
        },
        "module":
        {
            "title":"Add new module", 
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"module"
            },
            "help":
            {
                "command":"zf new module $module_name",
                "detail":"Creates a new module for project"
            }
        }
}