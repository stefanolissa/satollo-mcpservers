# MCP Servers

Create and publish MCP servers in WordPress.

With WordPress abilities provided by plugins, you can now make WP a source of actions
for your preferred AI agent (like Claude). But making those abilities available to
agents requires an MCP (Model Context Protocol) server.

With MCP Server for WordOPress, you can create one or more MCP servers and configure
them to expose selected abilities (by category or by single ability) so your agent can use them.

Why use different MCP servers and not exposing all the abilities on a single server?

It's usually recommended to have a small number of abilities for your agent so it can select
them wisely: giving too many tools, for different plugins, can create confusion. Expecially
is those tools has overlapping functionalities not clearly separated (for example,
"create a user", "create a subscriber").

## Creating an MCP server

It's very easy. From the administration page of the plugin, just add a new server, select a name and
the abilities you want to expose with that server. Done.

You'll get a server URL you can use on your AI agent. **If you open that URL with a browser you
get an error, it's ok!**.

## Connect an agent to an MCP server

WordPress exposes MCP servers using its REST API instrastructure and it provides by default
the Basic authentication method. It's recommended to use the "application passwords" you can
find on the user profile.

Many agents do not accepts the basic authentication, they may require the OAuth authentication method.
You can add it to WordPress using specialized plugins, like the OAuth Server CE.

Once you setup the authentication method, just add a new MCP server to your agent using the URL
provided by the MCP Servers plugin.

## Disclaimer

This plugin is an experiment, do not use it on production sites.

Abilities can execute destructive tasks it's your own responsibility to ask or not ask the
assistant to invoke them. A configuration is under work to enable/disable specific abilities.

## Prerequisites

WordPress 6.9.

## Install

Installation instructions [available here](https://www.satollo.net/plugins/mcpservers).

## Monitor plugin

To show a list of the available abilities, dump the input and output schema, log the executions
I adapted the plugin [Monitor](https://www.satollo.net/plugins/monitor).

## Assistant plugin

Add AI assistant both on the admin side and the frontend side selecting the abilities you
want to be expose. [Assistant](https://www.satollo.net/plugins/assistant).

