=== Satollo MCPServers ===
Contributors: satollo
Tags: ai,mcp
Requires at least: 6.9
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Configure and publish MCP servers to expose abiltities to AI agents

== Description ==

An MCP server exposes "abilities" that can be invoked by AI agents like Claude, Mistral, ChatGPT.

Abilities are provided by plugin with a standardized way to describe what they do and which parameters they need. It's a new feature of WordPress (6.9+).

For example a newsletter plugin can expose abilities to manage the subscribers or the newsletters.

A form manager can have abilities to manage forms or work with the collected submissions.

There are no limits and all those abilities can be used by an AI agent via the MCP protocol.

= Creating a server =

When you create a server, by default it does not expose any ability. You can choose one or more ability categories.

Or, you can even select single abilities.

Exposing all the abilities makes complicated by the AI agents to select what to invoke or, worse, there could be overlapping functionalities.

Hence, it's a good choice to limit the abilities to the ones you really need when interacting with your agent.

And you can create different MCP servers so you can enable on the agent side only the set of tools you need for your current work.

= Connecting to a server =

There are many way to let the AI agent connect to your MCP server(s). The simplest way is:

* create an administrator user named "mcp" and, in it's editing page, create an application password
* use "mcp" and the application password to setup the connection from the AI agent using the "Basic" authentication

Not all AI agents support the "Basic" authentication, for example Claude needs the oAuth2 authentication method.

You can install an oAuth2 plugin, like

= Tech details =

The plugin uses the WP MCP adapter library or the MCP Adapter plugin if installed, with priority to the latest one.

Once the Adapter plugin would be officially available, I'll add a dependency and the PHP library removed. authentication).

= Warning =

Abilities are registered an implemented by plugins and themes: check carefully what they do before exposing!

And be aware that permission checks are up to the single ability.

= References  =

* The [MCP Servers official page](https://www.satollo.net/plugins/mcpservers)
* The [MCP Adapater library/plugin](https://github.com/wordpress/mcp-adapter)
* Introduction to the [WP AI building blocks](https://make.wordpress.org/ai/2025/07/17/ai-building-blocks/)
* The [AI Experiments plugin](https://make.wordpress.org/ai/2025/07/17/ai-experiments-plugin/)
* The [Monitor plugin](https://www.satollo.net/plugins/monitor)

The AI Experiments plugin contains the "Ability explorer" a very useful tool.

== Changelog ==

= 1.0.1 =

* Readme fixes
* Debug info on the server editing page (with WP_DEBUG enabled)

= 1.0.0 =

* First release

