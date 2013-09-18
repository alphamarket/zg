{
        "project":
        {
            "title":"Create new project", 
            "detail":"Create a new zinux project", 
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"project"
            },
            "help":
            {
                "command":"zf new project $project_name",
                "detail":"Creates a new zinux project"
            }
        },
        "module":
        {
            "title":"Add new module", 
            "detail":"Create a new module for project", 
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