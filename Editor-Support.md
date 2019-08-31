Editor Support
==============

Features
--------

1. Real-time error detection from Phan (via daemon mode or language server protocol)
2. Ability to detect additional errors while typing, despite syntax errors.
3. Support for "Go to definition" and "Go to type definition" (For Language Server Protocol Clients)

Overview
--------

Three things need to be done to use Phan from an editor (with low latency).

1. Use an editor with Phan support, or modify a plugin or config to show Phan issues alongside of PHP syntax errors.
2. Manually start the Phan daemon/Phan Language Server for the project you are working on. These are two incompatible methods of running Phan in the background.

   1. The Phan daemon, which uses a custom Phan-specific protocol and was implemented first. The only client is `https://github.com/phan/phan/blob/master/phan_client`, which emulates the output of `php -l`. Setup instructions are included in [[Using Phan Daemon Mode]]
   2. The Phan Language server, which uses an open standard (The [Language Server Protocol](https://github.com/Microsoft/language-server-protocol))
3. Verify that `phan_client` (or the language server client) is working properly.

An alternative approach to editor support is to run Phan on the entire project whenever a file is saved, then parse and display the output.
This is noticeably slow on large projects because it has to parse thousands of files in the project directory.
This can be sped up with `--quick`. Alternate approaches are also discussed in [Using Phan Daemon Mode](https://github.com/phan/phan/wiki/Using-Phan-Daemon-Mode)

Editors with Phan support
-------------------------

Currently, there are clients of Daemon mode/Language Server Protocol for the following editors:

1. VS Code (Using the Phan language server)

   Unlike the editor Plugins using the daemon, this plugin will automatically start the Phan language server.

   [![VS Code example, including error tolerance](https://raw.githubusercontent.com/TysonAndre/vscode-php-phan/master/images/tolerant_parsing.png)](https://github.com/tysonandre/vscode-php-phan)

2. Vim 8.1+ and Neovim (Using the Phan language server. This is new )

   If needed, A newer version of vim can be installed with the instructions found at https://www.vim.org/download.php
   Alternately, you can use the [simpler snippet](https://github.com/phan/phan/blob/master/plugins/vim/phansnippet.vim) along with the Phan daemon.

   Unlike the editor Plugins using the daemon, this plugin will automatically start the Phan language server.

   [![VS Code example, including error tolerance](https://raw.githubusercontent.com/TysonAndre/LanguageServer-phan-neovim/master/images/tolerant_parsing.png)](https://github.com/tysonandre/LanguageServer-phan-neovim)

3. Vim: (Using Phan daemon) Run `phan_client` on save in Vim, show the results: https://github.com/phan/phan/blob/master/plugins/vim/phansnippet.vim

   This depends on the daemon being started in the background.

   This should work with Vim 7 and newer.

   [![Vim integration example](https://cloud.githubusercontent.com/assets/1904430/23336381/4210f212-fb83-11e6-9c55-79e0995307b1.png)](https://github.com/phan/phan/blob/master/plugins/vim/phansnippet.vim)

4. Emacs: (Using Phan daemon) Run `phan_client` while the file is being edited in Emacs. (Alternately, it can be configured to run only when saving a file):

   This depends on the daemon being started in the background.
   This also depends on flycheck(for Emacs) being installed.

   See https://github.com/TysonAndre/flycheck-phanclient

   [![Emacs flycheck example for Phan](https://cloud.githubusercontent.com/assets/1904430/23347092/85da0322-fc54-11e6-8fae-48b7a30d623b.png)](https://github.com/TysonAndre/flycheck-phanclient)

   For language server support (Adds "go to definition"), there is a WIP plugin at https://github.com/TysonAndre/lsp-phan

5. Sublime Text 3 (Using [Sublime LSP](https://github.com/tomv564/LSP))

   You need to have `phan` installed globally: `composer global require phan/phan` for this to work. You also need to make sure you have run `phan --init` in your project.
   Then you need the following config added to the LSP Settings page:

   ```
     // ... the LSP settings page
     "clients":
	{
                // you probably have some other clients in here
                "phan":
		{
			"command":
			[
				"phan",
				"--quick",
				"--allow-polyfill-parser",
				"--language-server-on-stdin",
				"--language-server-enable-hover",
				"--language-server-enable-completion",
				"--language-server-enable-go-to-definition",
			],
			"enabled": true,
			"initializationOptions":
			{
				"storagePath": "/tmp/phan"
			},
			"languageId": "php",
			"scopes":
			[
				"source.php",
				"embedding.php"
			],
			"syntaxes":
			[
				"Packages/PHP/PHP.sublime-syntax"
			]
		},
   ```

   That should be enough. The server should start without issues.

   ![Sublime Text 3 phan LSP](https://i.imgur.com/u2b44ug.png)

Adding support for other editors
--------------------------------

It may be possible to write an extension using Phan's Language Server Protocol support (And issue detection may be faster)

- The [VS Code extension](https://github.com/tysonandre/vscode-php-phan) uses most of this language server's features.
- Invoke `./phan --extended-help` to see a full list of CLI flags, including those useful when implementing language servers.

Another approach is to existing plugin or configuration which uses PHP's syntax checks (`--syntax-check`/`-l`) (e.g. `php -l [-f] path/to/file.php`) to use `phan_client` (`path/to/phan_client -l path/to/file.php`), and change the way it extracts error messages.

- (The error message format from `./phan_client` is almost the same, and `phan_client` run and outputs PHP's syntax check before requesting the Phan analysis results.)
