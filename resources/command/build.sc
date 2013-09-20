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
        "command":"zg build ($module_dir : modules) ( --root  ( $root_dir : .))",
        "alias": "zg b ($module_dir : modules)",
        "detail":"Builds a new zinux project configuration, based on currently exists zinux project.<br /> if no module directory supplied 'modules' would be targeted"
    },
    "options":
    {
        "--root":"Project's root directory (zg will analyzing this folder's sub-structures)."
    },
    "notes":
    [
        "This is a fail-safe command to restore zinux projects<br />that has lost their configuration file.",
        "You can use this command to rebuild the current project<br />config file if you ever messed-up with something.",
        "'zg build' only supports folder/file structures that is compatible<br />with standard zinux framework's defined directory structure."
    ]
}