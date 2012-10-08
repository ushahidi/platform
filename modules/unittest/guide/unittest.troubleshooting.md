# Troubleshooting

## I get the error "Class Kohana_Tests could not be found" when testing from the CLI

You need to running PHPUnit >= 3.4, there is a bug in 3.3 which causes this.

## Some of my classes aren't getting whitelisted for code coverage even though their module is

Only the "highest" files in the cascading filesystem are whitelisted for code coverage.

To test your module's file, remove the higher file from the cascading filesystem by disabling their respective module.

A good way of testing is to create a "vanilla" testing environment for your module, devoid of anything that isn't required by the module.

## I get a blank page when trying to generate a code coverage report

Try the following:

1. Generate a html report from the command line using `phpunit {bootstrap info} --coverage-html ./report {insert path to tests.php}`.  If any error messages show up, fix them and try to generate the report again
2. Increase the php memory limit
3. Make sure that display_errors is set to "on" in your php.ini config file (this value can sometimes be overriden in a .htaccess file)
