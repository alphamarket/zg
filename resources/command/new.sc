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
        "title":"Add new controller", 
        "alias":"c",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"controller"
        },
        "help":
        {
            "command":"zg new controller $controller_name ($module_name)",
            "alias": "zg n c $controller_name ($module_name)",
            "detail":"Creates a new controller for a module."
        },
        "defaults":
        {
            "$module_name" : "default"
        }
    },
    "action":
    {
        "title":"Add new action", 
        "alias":"a",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"action"
        },
        "help":
        {
            "command":"zg new action $action_name ($controller_name) ($module_name)",
            "alias": "zg n a $action_name ($controller_name) ($module_name)",
            "detail":"Creates a new action for a controller in a module."
        },
        "defaults":
        {
            "$controller_name" : "index",
            "$module_name" : "default"
        }
    },
    "view":
    {
        "title":"Add new view", 
        "alias":"v",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"view"
        },
        "help":
        {
            "command":"zg new view $view_name ($controller_name) ($module_name)",
            "alias": "zg n v $view_name ($controller_name) ($module_name)",
            "detail":"Creates a new view for a controller in a module."
        },
        "defaults":
        {
            "$controller_name" : "index",
            "$module_name" : "default"
        }
    },
    "layout":
    {
        "title":"Add new layout", 
        "alias":"l",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"layout"
        },
        "help":
        {
            "command":"zg new layout $layout_name ($module_name)",
            "alias": "zg n l $layout_name ($module_name)",
            "detail":"Creates a new layout for a module."
        },
        "defaults":
        {
            "$module_name" : "default"
        }
    },
    "model":
    {
        "title":"Add new model", 
        "alias":"m",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"model"
        },
        "help":
        {
            "command":"zg new model $model_name ($module_name)",
            "alias": "zg n m $model_name ($module_name)",
            "detail":"Creates a new model for a module."
        },
        "defaults":
        {
            "$module_name" : "default"
        }
    },
    "helper":
    {
        "title":"Add new helper", 
        "alias":"h",
        "instance":
        {
            "class":"\\zinux\\zg\\resources\\operator\\_new",
            "method":"helper"
        },
        "help":
        {
            "command":"zg new helper $helper_name ($module_name)",
            "alias": "zg n h $layout_name ($module_name)",
            "detail":"Creates a new helper for a module."
        },
        "defaults":
        {
            "$module_name" : "default"
        },
        "notes":
        [
            "In order to have free uses in models there will be NO<br />naming convention when creating models.<br />i.e the command 'zg new model foo $module_name'<br />will exactly creates a model named 'foo' in target module."
        ]
    },
    "application":
    {
        "alias":"app",
        "bootstrap":
        {
            "title":"Creates a new bootstrap file", 
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
                "detail":"Creates a bootstrap file for project with given name"
            }
        },
        "routes":
        {
            "title":"Creates a new routes file", 
            "alias":"r",
            "instance":
            {
                "class":"\\zinux\\zg\\resources\\operator\\new_app",
                "method":"routes"
            },
            "help":
            {
                "command":"zg new application routes $routes_name",
                "alias": "zg n app r $routes_name",
                "detail":"Creates a routes file for project with given name"
            }
        }
    }
}