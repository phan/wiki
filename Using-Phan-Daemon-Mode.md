Introduction
============

Description
-----------

Daemon mode lets you use Phan from your editor or IDE to detect Phan issues on a single file, with much lower latency (< 0.1 seconds). This is useful on large codebases with hundreds or thousands of PHP files, where the full analysis would take minutes to run.

**This is a draft, and daemon mode requires Phan 0.9.2 (not yet released) or dev-master**

Daemon mode was first introduced to Phan in https://github.com/etsy/phan/pull/563

There are two components:

1. The Phan daemon (i.e. server) (`./phan --daemonize-tcp-port 4846 --quick`) which responds to TCP requests.

   The basic way that it works is that it pauses at the parse phase, and then runs the analysis phase on only a single file (or small list of files).

   It accepts requests over TCP, then scans and re-parses files to get the new class/method/function/constant/property definitions when the code base changes, and then forks a new process which will analyze the requested file(s) and send the response.
2. The Phan client (`./phan_client`), which queries the server (over TCP) to quickly get Phan's analysis result for a single file.

   (Or outputs errors without querying the server, if the file does not exist or has PHP syntax errors)

Configuration
-------------

The same .phan/config.php works for the daemon mode.

It's recommended to pass `--quick` when starting the daemon. Analysis would take longer if Phan also analyzes the functions/methods that are called by the file(s) being analyzed.

Requirements
------------

1. The latest etsy/phan dev-master, or the 0.9.2 release or newer.
2. Unix(e.g. Mac) or Linux, to run the daemon.

To run the Phan daemon, you must have the pcntl extension installed (Requires that extension to be installed and enabled in PHP).

This doesn't work natively in Windows, due to the dependency on pcntl.

- It may be possible for Windows users to run the Phan daemon from inside of docker, using a volume and exposing port 4846 from the container (May also need to change the daemon code to listen for connections on all interfaces instead of 127.0.0.1 (localhost))
  (See https://docs.docker.com/docker-for-windows/)

Recommendations for performance
-------------------------------

If Phan daemon mode is noticeably slow, then make sure that you're following all of the recommendations for performance.
The Phan daemon will warn on startup if you're using xdebug or using PHP compiled with `--enable-debug` (If it doesn't warn, those settings are fine)

1. Start the daemon with `--quick`, optionally disable any plugins.
   
   In non-quick mode, methods and classes from other files would also be analyzed, making the request take several times longer.
2. Don't use xdebug when starting the daemon. If you have xdebug enabled, then disable it (or provide a path to an alternate php.ini with xdebug disabled)
   (Client requests would take about 10 times as long)
3. Don't use PHP compiled with `--enable-debug`. If php was compiled with `--enable-debug`, client requests would take about 2 times as long.
   (Install a different, non-debug version of PHP, and start the Phan daemon using the non-debug version of PHP)

   Note: If a different PHP installation is used, you may want to install and enable the same extensions in order for Phan to not warn you about the extensions' classes and functions being undefined.
4. Make sure that the Phan config only includes direct dependencies of your project from `vendor/`,
   and skips indirect dependencies.
   (i.e. the folders which contain declarations of classes used in analyzed code and phpdoc of analyzed code. The full analysis would warn you if any of those classes were removed, via a PhanUndeclaredClass issue)

   Exclude test, documentation, and example folders in `vendor/` using `exclude_file_regex`.
   (The Phan daemon has to scan all of the directories and files in the parse list, to check for file modifications before analyzing. Re-parsing files takes a large fraction of the time for a request.)

Using phan_client from an editor
================================

Three things need to be done to use the Phan client from an editor.

1. Use an editor with Phan support, or modify a plugin or config to show Phan issues alongside of PHP syntax errors.
2. Manually start the Phan daemon for the project you are working on.
3. Verify that `phan_client` is working properly.

### 1. Editors with phan support
Currently, there are clients of Daemon mode for the following editors:

1. Vim: Run phan_client on save in Vim, show the results: https://github.com/etsy/phan/blob/master/plugins/vim/phansnippet.vim

   
   ![screenshot from 2017-02-23 22-29-21](https://cloud.githubusercontent.com/assets/1904430/23336381/4210f212-fb83-11e6-9c55-79e0995307b1.png)


2. Emacs: Run phan_client while the file is being edited in Emacs. (Alternately, it can be configured to run only when saving a file):

   This depends on flycheck being installed.

   See https://github.com/TysonAndre/flycheck-phanclient

   ![flycheck_phan_example](https://cloud.githubusercontent.com/assets/1904430/23347092/85da0322-fc54-11e6-8fae-48b7a30d623b.png)
3. Other: Try to adapt an existing plugin or configuration which uses php's syntax checks (`--syntax-check`/`-l`) (e.g. `php -l [-f] path/to/file.php`) to use `phan_client` (`path/to/phan_client -l path/to/file.php`).

   (The error message format from `./phan_client` is almost the same, and `phan_client` run and outputs PHP's syntax check before requesting the Phan analysis results.)

### 2. Starting the Phan daemon

The Phan daemon must be manually started if you want to use it from your editor.

If you're only working on one project, start it with the default client port of 4846.

It must be started again if it was stopped (e.g. stopped manually, crashed, or the computer was restarted)

(If you plan to work on multiple projects at the same time, choose different ports for each project, and start an instance of the daemon for each project)

Editor integration requires you to manually start the Phan daemon for the project you are working on.

To start Phan with the port clients use by default (and with low latency), execute the following command from the root of the directory being analyzed:

```bash
# e.g. vendor/bin/phan if it's installed as a project dependency.
# Note: you may want to install Phan into a separate directory from the PHP projects you will be working on,
# to avoid accidentally modifying or deleting files while it is running.
path/to/phan --daemonize-tcp-port 4846 --quick
```

### 3. Using phan_client

By default, `phan_client` uses port 4846, so the port flag is optional for the client.

If you plan to work on multiple PHP projects and use phan at the same time:

- You may need to make changes to check which project this is in before passing `--daemonize-tcp-port <portnumber>` if you plan to work on multiple PHP projects
     
  One way to do this is to make a copy of `phan_client` which would choose the port number based on the project path for you)

  Long term, something like `$HOME/.phan_client_config` may be introduced to support that use case (mapping folders for projects to the port number)



Long term, https://github.com/Microsoft/language-server-protocol seems to make more sense to support, starting with issue detection.

#### Checking that the server works and that phan_client can query it

Prerequisites:

1. The project has a `.phan/config.php` file
2. The phan daemon is started inside of the root of that project (`path/to/phan --daemonize-tcp-port 4846 --quick`)
   (It output a message to the console saying that it is accepting connections on port 4846)

```bash
# 1. Edit a file to include a call to an undefined function, etc. as a sanity check
# Invoke phan (`./phan_client` or `php ./phan_client`) with a relative or absolute path to the file being analyzed.
# NOTE: This must be a file which is in the phan config's list of files/directories to analyze
./phan_client --verbose path/to/file/in_project.php
# 2. You should see it emit errors, in a format similar to the format `php -l` uses
# 3. Undo changes to the file, run phan again
./phan_client --verbose path/to/file/in_project.php
# 4. The errors should not exist in the second run.
# 5. Try out one of the editor plugins
```

### Known bugs and limitations

Occasionally, daemon mode will lose track of definitions for classes/methods, e.g. when switching between git checkouts.
In that case, stop the phan daemon and start it again.

The Phan config won't be reloaded if it changes.

### FAQ

**Why use pcntl and forked processes for the daemon?**:
Phan is under development, and the data representation it uses is frequently modified.
There is some caching done by the analysis mode, which would be hard to undo, and take a lot of time).
This approach has the best chance of working, without interfering with (or being accidentally broken by) the development of Phan's analysis implementation.

This is similar to the reason why the sqlite3 database is no longer included.