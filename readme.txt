=== Satollo MCP Servers ===
Contributors: satollo
Tags: ai,mcp,abilities,ai agent
Requires at least: 6.9
Tested up to: 6.9
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Configure and publish MCP servers to expose abiltities to AI agents

== Description ==

The MCP Servers plugin enables WordPress to expose "abilities" - standardized functions that AI agents (such as Claude, Mistral, or ChatGPT) can invoke.

These abilities allow plugins and themes to describe their functionalities and required parameters in a structured way, facilitating seamless interaction with AI agents via the MCP (Model Context Protocol).


= Use Cases =

* A newsletter plugin can expose abilities to manage subscribers or newsletters.
* A form manager can provide abilities to handle forms or process submissions.
* No limits: Any functionality can be exposed and utilized by AI agents.


= Creating a server =

Go to the "Servers" page and add a server. By default, a new MCP server does not expose any abilities. You can:

* Select one or more ability categories.
* Choose individual abilities to expose.

= Best Practices =

* Avoid exposing all abilities: This can overwhelm AI agents and lead to overlapping functionalities.
* Limit abilities to those necessary for your current workflow.
* Create multiple MCP servers to enable only the tools required for specific tasks on the agent side.


= Connecting to a server =

AI agents can connect to your MCP server(s) in several ways. The simplest method is:

* Create an administrator user named "mcp".
* Generate an application password for this user in the WordPress dashboard.
* Use Basic Authentication with the mcp username and the application password to set up the connection from the AI agent.

= Note on Authentication =

Not all AI agents support Basic Authentication. For example, Claude requires OAuth2.

To enable OAuth2, install a compatible plugin (e.g., WP OAuth Server).


= References  =

* The [MCP Servers official page](https://www.satollo.net/plugins/mcpservers)
* The [MCP Adapater library/plugin](https://github.com/wordpress/mcp-adapter)
* Introduction to the [WP AI building blocks](https://make.wordpress.org/ai/2025/07/17/ai-building-blocks/)
* The [AI Experiments plugin](https://make.wordpress.org/ai/2025/07/17/ai-experiments-plugin/)
* The [Monitor plugin](https://www.satollo.net/plugins/monitor)

The AI Experiments plugin contains the "Ability explorer" a very useful tool.


= Tech details =

The plugin uses the WP MCP Adapter library or the MCP Adapter plugin (if installed), with priority given to the latter.

Once the Adapter plugin is officially available, it will be added as a dependency, and the PHP library will be removed.


= Warning =

Abilities are registered an implemented by plugins and themes: check carefully what they do before exposing!

And be aware that permission checks are up to the single ability.

== Changelog ==

= 1.0.1 =

* Readme fixes
* Debug info on the server editing page (with WP_DEBUG enabled)
* Fixed the category list on database

= 1.0.0 =

* First release

