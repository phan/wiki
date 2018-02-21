Introduction
============

Description
-----------

Daemon mode lets you use Phan [from your editor or IDE](https://github.com/phan/phan/wiki/Editor-Support#editor-support) to detect Phan issues on a single file (or small set of files), with much lower latency than a full analysis (< 0.3 seconds).
This is useful on large codebases with hundreds or thousands of PHP files, where the full analysis could take minutes to run.

Daemon mode requires Phan 0.10.0+ or 0.8.4+.

Daemon mode was first introduced to Phan in https://github.com/phan/phan/pull/563

There are two components:

1. The Phan daemon (i.e. server) (`./phan --daemonize-tcp-port 4846 --quick`) which responds to TCP requests.

   The Phan daemon pauses at the parse phase, and then runs the analysis phase on only a single file (or small list of files).
   (If any files change, the daemon removes any state changes (e.g. class definitions, function definitions, emitted issues, etc.) caused by removed/modified files, and adds state changes caused by added/modified files.

   The daemon accepts requests over TCP, then scans and re-parses files to get the new class/method/function/constant/property definitions when the code base changes, and then forks a new process which will analyze the requested file(s) and send the response.
2. The Phan client (`./phan_client`), which queries the server (over TCP) to quickly get Phan's analysis result for a single file.

   (Or outputs errors without querying the server, if the file does not exist or has PHP syntax errors)

Configuration
-------------

The same `.phan/config.php` works for the daemon mode.

It's recommended to pass `--quick` when starting the daemon. Analysis would take longer if Phan also analyzes the functions/methods that are called by the file(s) being analyzed.

It's also recommended to install Phan in a directory outside of your project when using daemon mode. If it's part of the project (or committed in version control), Phan may crash if you switch between branches, since a different branch may have a different Phan version or no Phan version. (or due to `git clean`)

- Starting Phan daemon mode with an external phan installation can be done using the following commands:

  `cd /path/to/analyzed_project; /path/to/phan_checkout/phan --daemonize-tcp-port 4846 --quick`

  (Would need to have run `composer.phar install` in the external phan checkout first)

Requirements
------------

1. Phan 0.10.0+/0.8.4+
2. Unix(e.g. Mac) or Linux with pcntl enabled, to run the daemon.

To run the Phan daemon, you must have the pcntl extension installed (Requires that extension to be installed and enabled in PHP).

This doesn't work natively in Windows, due to the dependency on pcntl.

- It may be possible for Windows users to run the Phan daemon from inside of docker, using a volume and exposing port 4846 from the container (May also need to change the daemon code to listen for connections on all interfaces instead of 127.0.0.1 (localhost))
  (See https://docs.docker.com/docker-for-windows/)

Recommendations for performance
-------------------------------

If Phan daemon mode is noticeably slow, then make sure that you're following all of the recommendations for performance.



1. Start the daemon with `--quick`, optionally disable any plugins.

   In non-quick mode, methods and classes from other files would also be analyzed, making the request take several times longer.
2. Follow the steps from the wiki page [[Speeding up Phan Analysis]].

Editor Support
==============

### 1. Editor Support

See [[Editor Support]].

The below sections elaborate on how to start the Phan daemon for your editor, if the plugin requires it.

### 2. Starting the Phan daemon

The Phan daemon must be manually started if you want to use it from your editor, for plugins using phan daemon mode (instead of the Phan Language Server or whole-project analysis).

If you're only working on one project, start it with the default client port of 4846 (`--daemonize-tcp-port 4846` or `--daemonize-tcp-port default`)

It must be started again if it was stopped (e.g. stopped manually, crashed, or the computer was restarted).
Currently, it that may require a restart to be aware of new classes.

(If you plan to work on multiple projects at the same time, choose different TCP ports for each project, and start an instance of the daemon for each project)

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
2. The Phan daemon is started inside of the root of that project (`path/to/phan --daemonize-tcp-port 4846 --quick`)
   (When the daemon is done setting up, it output a message to the console saying that it is accepting connections on port 4846)

```bash
# 1. Edit a file to include a call to an undefined function, etc. as a sanity check
# Invoke phan (`./phan_client` or `php ./phan_client`)
# with a relative or absolute path to the file being analyzed.
# NOTE: This must be a file which is in the
# phan config's list of files/directories to analyze
./phan_client --verbose path/to/file/in_project.php
# 2. You should see it emit errors, in a format similar to the format `php -l` uses
# 3. Undo changes to the file, run phan again
./phan_client --verbose path/to/file/in_project.php
# 4. The errors should not exist in the second run.
# 5. Try out one of the editor plugins
```

### Known bugs and limitations

Occasionally, daemon mode will lose track of definitions for classes/methods, e.g. when switching between git checkouts.
In that case, stop the Phan daemon and start it again.

The Phan config won't be reloaded if it changes.

### FAQ

**Why use pcntl and forked processes for the daemon?**:
Phan is under development, and the data representation it uses is frequently modified.
There is some caching done by the analysis mode, which would be hard to undo, and take a lot of time).
This approach has the best chance of working, without interfering with (or being accidentally broken by) the development of Phan's analysis implementation.

This is similar to the reason why the sqlite3 database is no longer included.

An upcoming release of phan will let daemon mode work without `pcntl`.
