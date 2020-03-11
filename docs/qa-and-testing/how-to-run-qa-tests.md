# How to run QA tests

Running of tests is usually dictated by a checklist that can be found on test cases that reside on TestPad, or a checklist that is found on the issue/task card.

**From issue/task card**

Tests will be run for a specific issue/task when only that single issue is being tested. This will happen throughout the development cycle on a need-to basis i.e whenever an issue is ready to be tested.

For instances where the checklist is on the issue/task card, once the issue is ready for testing and has been deployed then the tester will follow these list ticking off every item as they pass. A list that completely passes has all items in the list ticked off. Where an item in the list fails, this is left unmarked to indicate that it did not pass. Where relevant, additional information will be left in the comment section of the issue/task card. The relevant developer will be alerted and they can follow up, knowing what item failed to pass.

**From TestPad**

TestPad houses a comprehensive test suite that cover most parts of the platform application. You’ll turn to TestPad when you’re running either smoke or regression tests.

[Smoke testing](https://ushahidi.ontestpad.com/script/30#//) usually happens before a release. It's usually quick and looks at the core functionality of the application to verify the main application flows are working.

[Regression tests](https://ushahidi.ontestpad.com/script/27#//) are more detailed and cover the application in its entirety.

Tests in TestPad are structured in a checklist format. As you run the tests,you mark off every item as either passing or failing.

![](https://lh6.googleusercontent.com/fnppiTQR_KbfZ231wPL9xR2qUhMbDcb2SZDE-kHm7HWaD_N7A5yzE9BU23HtvSX1hxXPROYP79eX6ZGPI_v0q8qg5JU9WE5Fjm6-Ani6AtRKFmWuZ24hr5aPS6off2agz5flE8GC)

TestPad also allows for more details on a test including when the test was run, who run it, and relevant statistics i.e passing tests and failing tests as percentages.

