{
        "alias":"n",
        "title":"Create new project",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"project"
        },
        "help":
        {
            "command":"zg new $project_name",
            "alias": "zg n $project_name",
            "detail":"Creates a new zinux project"
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
            "title":"Add new controller to a module", 
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
        },
        "action":
        {
            "title":"Add new action to a controller", 
            "alias":"a",
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\_new",
                "method":"action"
            },
            "help":
            {
                "command":"zg new action $action_name ($controller_name : index) ($module_name : default)",
                "alias": "zg n a $controller_name ($controller_name : index) ($module_name : default)",
                "detail":"Creates a new controller for module, if no module or no controller name supplied 'defaultModule' or 'indexController' will be targeted"
            }
        },
        "application":
        {
            "alias":"app",
            "bootstrap":
            {
                "title":"Creates a new bootstrap file for application", 
                "alias":"b",
                "instance":
                {
                    "class":"\\zinux\\zg\\resources\\operator\\new_app",
                    "method":"bootstrap"
                },
                "help":
                {
                    "command":"zg new application boostrap $bootstrap_name",
                    "alias": "zg n app b $bootstrap_name",
                    "detail":"Creates a bootstrap file with given name"
                }
            }
        }
}