{
    "title":"Build a new zg configuration", 
    "alias":"b",
    "instance":
    {
        "class":"\\zinux\\zg\\resources\\operator\\build",
        "method":"build"
    },
    "help":
    {
        "command":"zg build ( -m ($module_dir : modules) ) ( -a  ( $app_dir : application) ) ( -p  ( $project_dir : .) )",
        "alias": "zg b ( -m ($module_dir : modules) ) ( -a  ( $app_dir : application) ) ( -p  ( $project_dir : .) )",
        "detail":"Builds a new zinux project configuration, based on currently existed zinux project.<br /> if no module directory supplied 'modules' would be targeted"
    },
    "options":
    {
        "-p":"Project's root directory.<br />(zg will analyzing this folder's sub-structures.)",
        "-a":"Project's application directory, a relative path from '-p' argument.",
        "-m":"Modules' root directory, a relative path from '-p' argument.<br />(zg will consider this folder's sub-structures as project's modules.)"
    },
    "notes":
    [
        "This is a fail-safe command to restore zinux projects<br />that has lost their configuration file.",
        "You can use this command to rebuild the current project<br />config file if you ever messed-up with something.",
        "'zg build' only supports folder/file structures that is compatible<br />with standard zinux framework's defined directory structure."
    ]
}