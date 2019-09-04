# QA & Testing

### Testing new code

Ushahidi is doing QA and running manual tests on all issues merged into the codebase. Depending on the module that is being changed the tests may vary for user facing changes the test list will often include clear usecase tests, while API side changes may only require the execution of the automated tests.  
  
****In all cases, the developer adding a pull request must write down and then run their own test list before asking another person to review the code, this ensures basic mistakes are caught and avoids the reviewer using time on simple issues that could be solved without their review.

At the moment, the tests form the basis for QA work when testing a new release. These tests should be explicit and detailed and related to the changes made by the PR, where relevant the Tests should reference existing test cases that might be impacted by the changes so that QA can explicitly runs those tests as well.

### Regression and smoke tests

**Smoke tests**

Before every release we run smoke-tests to quickly verify the build's critical path is working. 

#### Regression tests

Big changes that we think may break the app in unexpected ways that cannot be verified through smoke testing only. For example API Changes that required client modifications for them to not break or big features touching many parts of the application.

#### **Where do tests live?**

We store our tests in Testpad, if you want to contribute by doing QA and testing, please contact us on [techdocs@ushahidi.com](mailto:techdocs@ushahidi.com) to get access to the test suits.

* [ ] Testing
* [ ] 


