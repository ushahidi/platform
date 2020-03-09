# The QA process

The QA process for Platform involves both the QA team and the developers. After a developer has worked on an issue/task and it is ready, then this is released to a test environment. Manual tests are then run by the QA team on the test environment. Tests are guided by a checklist that is ideally written up by the developer and reside on the issue on GitHub. This checklist can also be written up by the QA team.

After tests have been run, if issues are found, they are documented on GitHub with as much relevant information as possible. Issues can be either a feature request/improvement, or a bug. An issue is a feature request/improvement if the application is working as expected, but could use this to make the application work better, or improve it in any other way. A bug on the other hand is something that gets in the way of normal functionality of the application. The developer that worked on the issue is tagged and the issue is given the relevant label, whether "bug" or “feature request/improvement”.

If no issues are found, then that issue is tagged as done and is ready to be merged into production-aws and master.

