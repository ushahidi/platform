# How to write QA test scripts

Test cases reside in two different locations for Platform.

1 . TestPad - this is our test management tool and this is where the test cases officially reside. The tests here are majorly developed by the QA team. They are detailed and cover most parts of the application.  


2. Github issues/tasks - these tests are short and straight to the point. They are more a guide to what should be looked at when testing the feature. They are ideally created by the developer that works on the feature. The product owner can also create this. They are designed to take a short time and test a very specific feature or part of the application.  


While writing up these tests, it’s important to keep in mind:

* Core actions to be performed during execution
* Brevity - each test step should be a single action. This makes it easy to track failures
* Grouping/categories - test cases for specific actions should be categorized together. This makes it easy to follow along, and find specific tests for specific actions.

Tip: When writing test cases, you want to consider two broad scenarios: 

1. Functionalities of the application \(also known as Happy Path\) - these test cases cover basic\(expected\) steps and outcomes from usage of the application.
2. Edge cases - these test cases cover scenarios that are highly unlikely to occur during normal usage of the application. An instance would be resizing a browser window with the application open, navigating using a keyboard as opposed to mouse + cursor, network interruptions when uploading/downloading content etc.

Additions, improvements and suggestions are welcome to our test suite. Should you come across any section that you feel need any sort of adjustment, feel free to reach out to the team. We are more than happy to listen to any thoughts or feedback. :\)   


**Sample QA Test scripts**

The test scripts are written in simple English, and they are usually steps or actions to be taken as the testing process.  
****

**Hotfixes**

A hotfix is a fix to an issue that has been discovered on production that needs to be fixed ASAP. 

\*\*\*\*

