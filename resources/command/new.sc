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
                "command":"zg new project $project_name",
                "alias": "zg n p $project_name",
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
                "command":"zg new module $module_name",
                "detail":"Creates a new module for project"
            }
        },
        "controller":
        {
            "title":"Add new module", 
            "alias":"c",
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"controller"
            },
            "help":
            {
                "command":"zg new controller $controller_name ($module_name : default)",
                "alias": "zg n c $controller_name ($module_name : default)",
                "detail":"Creates a new controller for module, if no module name supplied 'defaultModule' will be targeted"
            }
        }
}