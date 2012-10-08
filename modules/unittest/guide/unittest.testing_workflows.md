# Testing workflows

Having unittests for your application is a nice idea, but unless you actually use them they're about as useful as a chocolate firegaurd.  There are quite a few ways of getting tests "into" your development process and this guide aims to cover a few of them.

## Integrating with IDEs

Modern IDEs have come a long way in the last couple of years and ones like netbeans have pretty decent PHP / PHPUnit support.

### Netbeans (6.8+)

*Note:* Netbeans runs under the assumption that you only have one tests folder per project.  
If you want to run tests across multiple modules it might be best creating a separate project for each module.

0. Install the unittest module

1. Open the project which you want to enable phpunit testing for.

2. Now open the project's properties dialouge and in the "Tests Dir" field enter the path to your module's (or application's) test directory.  
   In this case the only tests in this project are within the unittest module

3. Select the phpunit section from the left hand pane and in the area labelled bootstrap enter the path to your app's index.php file

You can also specify a custom test suite loader (enter the path to your tests.php file) and/or a custom configuration file (enter the path to your phpunit.xml file)

## Looping shell

If you're developing in a text editor such as textmate, vim, gedit etc. chances are phpunit support isn't natively supported by your editor.

In such situations you can run a simple bash script to loop over the tests every X seconds, here's an example script:

	while(true) do clear; phpunit; sleep 8; done;

You will probably need to adjust the timeout (`sleep 8`) to suit your own workflow, but 8 seconds seems to be about enough time to see what's erroring before the tests are re-run.

In the above example we're using a phpunit.xml config file to specify all the unit testing settings & to reduce the complexity of the looping script.

## Continuous Integration (CI)

Continuous integration is a team based tool which enables developers to keep tabs on whether changes committed to a project break the application. If a commit causes a test to fail then the build is classed as "broken" and the CI server then alerts developers by email, RSS, IM or glowing (bears|lava lamps) to the fact that someone has broken the build and that all hell's broken lose.

The two more popular CI servers are [Hudson](https://hudson.dev.java.net/) and [phpUnderControl](http://www.phpundercontrol.org/about.html), both of which use [Phing](http://phing.info/) to run the build tasks for your application.
