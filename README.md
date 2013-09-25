zg
==

[Zinux](https://github.com/dariushha/zinux) Generator Tool
--
This is a tool designed to create, manipulate <i>[zinux](https://github.com/dariushha/zinux)</i> projects. <i>Zinux generator tool</i> is an efficient appliction designed to make use of <i>zinux</i>
project even easier than it is, and also makes you develop more, in a short time.<br />

> From now on we will refer to <b><i>Zinux Generator Tool</i></b> by <b><i>zg</i></b>. 

Topics
--
* [Requirements](#requirements)
* [Installation](#installation)
  * [Windows Users](#windows-users) 
* [Commands Types](#command-types)
	* [<b><i>Version</i></b>](#version)
	* [<b><i>Help</i></b>](#help)
	* [<b><i>New</i></b>](#new)
		* [New Project](#new-project)
		* [New Module](#new-module)
		* [New Controller](#new-controller)
		* [New Action](#new-action)
		* [New View](#new-view)
		* [New Layout](#new-layout)
		* [New Model](#new-model)
		* [New Helper](#new-helper)
		* [New Application](#new-application)
			* [New Appliction Bootstrap](#new-application-bootstrap)
			* [New Appliction Routes](#new-application-routes)
	* [<b><i>Remove</i></b>](#remove)
	* [<b><i>Build</i></b>](#build)
	* [<b><i>Config</i></b>](#config)
	* [<b><i>Security</i></b>](#security)
	* [<b><i>Status</i></b>](#status)
	* [<b><i>Update</i></b>](#update)

Requirements
--
* PHP version 5.3.10 or greater
* [Zinux](https://github.com/dariushha/zinux) Project 3.0.0

Installation
--
There is a [zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer) shell script, it will 
automatically download and configure your system to use <i>zinux</i> project freely in your system.<br />
It also installs [zinux generator tool](https://github.com/dariushha/zg) which is an handy tool to create, manipulate
and provides solid security for your <i>zinux</i> project, for more information see
[Zinux Generator Tool](#zinux-generator-tool).

> You will need to have [Git](http://git-scm.com/) installed before using
[zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer). 

For installation just download the [zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer) 
and save it at anywhere, and run the following command `bash /path/to/your/zinux/installer` it will do the reset. 

Windows Users
--
For running shell scripts in Windows you need a third-party application installed in your system 
to enable using shell scripts in Windows, such as:
* [GnuWin32](http://gnuwin32.sourceforge.net/)
* [UnxUtils](http://unxutils.sourceforge.net/)

> <b>Note:</b> [zinux generator tool](https://github.com/dariushha/zg) also uses shell scripts to run faster, 
so before you use [zinux installer](https://raw.github.com/dariushha/zinux/master/zinux-installer) or start using
[zinux generator tool](https://github.com/dariushha/zg) make sure your Windows supports shell scripts, i.e if 
`ls -l` command lists your directories, you are OK! 

Command Types
==
The <i>zg</i> uses very simple and flexible command lines. Except [Security](#security) command line which is an 
sensitive command all other commands has aliases which is the short form of original command. A list of available 
commands and their details is as follow:<br />

Version
--
<b>Title</b><br />
Shows Version.
<hr />
<b>Description</b><br />
Show both Zinux's and Zinux Generator's versions.
<hr />
<b>Command</b><br />
```PHP
zg --version
```
<hr />

Help
--
<b>Title</b><br />
Prints help content.
<hr />
<b>Description</b><br />
Prints help content.
* It can be general help, which will print all commands help content.
* It can be specific, which will print only the help content of target command.

<hr /> 
<b>Command</b><br />
```PHP
zg -h ($command) (--heads)
```
<hr />
<b>Alias</b><br />
```PHP
zg -h ($command_alias) (--heads)
```
<hr />
<b>Optionals</b><br />
* <b>$command</b> : print a specific command's help content.
* <b>--heads</b>  : if you pass this argument it will only print valid command lines under `$command` command line.

<hr />
<b>Default Values</b><br />
* <b>$command</b> : If you don't pass `$command` it will print all commands' help content. 

<hr />
<b>Notes</b><br />
* `$command` should be a valid command in `zg` command list.

<hr />
<b>Examples</b><br />
```PHP
# prints all commands' help content
zg
# OR using aliases :
zg -h
```
```PHP
# prints 'zg new'command's help content
zg -h new 
# OR using aliases :
zg -h n
```
<b>Or you can be more specific, like:</b>
```PHP
# prints 'zg new action' command's help content
zg -h new action
# OR using aliases :
zg -h n a
```
<hr />
New
--
New Project
--
<b>Title</b><br />
Creat new project.
<b>Description</b><br />
This command will creates new project and its initial files and directories such as:
* application
	* appBootstrap.php
	* appRoutes.php 
* module/defaultModule
	* controller
		* indexController.php
	* views/layout
		* defaultLayout.phtml
	* views/view/index
		* indexView.phtml
	* defaultBootstrap.php	
* public_html
	* index.php
	* .htaccess
* [zinux](https://github.com/dariushha/zinux) framework

<hr />
<b>Command</b><br />
```PHP
zg new project $project_name (--empty)
```
<hr />
<b>Alias</b><br />
```PHP
zg new $project_name (--empty)
```
<hr />
<b>Optionals</b><br />
* <b>--empty</b> : By passing this option it will create an empty project without any modules or application directory.
<hr /> 
<b>Examples</b><br />
```PHP
# creates new project direcroty named 'test
zg new project test
# or using aliases: 
zg n test
```
<hr />

Remove
--
Build
--
Config
--
Security
--
Status
--
Update
--
