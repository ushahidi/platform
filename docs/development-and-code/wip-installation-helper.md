# ⚙️ Installation-helper

To make it easier for users to install and help solve common problems during the installation-process, there is an installation-helper that you can use.

### 1. Clone the platform-client repository:

The code for the installer-helper is bundled together with the platform-client so to run it, you need to clone the repository, follow the installation of the Platform-client here:

{% page-ref page="setup\_alternatives/setting-up-the-platform-client.md" %}

### 2. Access the installation-helper

There are two ways of accessing the installer helper, from the command-line or as a browser-version.

**Command-line:**

Open a terminal-window, navigate to the folder where your platform-client code is and run:

`gulp verify`

After doing this, the installation-helper runs checks and displays the results in the command-line. If there are any errors, the helper displays hints on how to solve the problem.

**Browser:**

In order to access the browser-version, go to the command-line again, make sure you are in the same folder as the platform-client code and run :

`gulp dev:verifier`

then navigate to localhost:3000/verifier.

The installation-helper runs the same checks as in the command-line, but displays the result as a web-page. The hints and checks are the same as in the command-line version.

**Helper in the api**

Both checks for api and client configuration is checked in the installation helper in the client. If you are only interested in installing the api, there is a command-line helper there as well that checks the database connectivity and environment variables. Once you have cloned the api you can open up a terminal window, navigate to the folder where you have the platform-api code and run:

 `composer verify`

After this, the installation-helper does the checks and prints out the result in the terminal

