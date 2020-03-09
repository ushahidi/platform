---
description: Example user testing scripts to conduct user testing from.
---

# User testing script examples

## Testing Plan

You always need a plan before you conduct a user test. A plan will define who you are testing, as well as the structure of each session \(another word for this is a “research protocol”\).

Ushahidi typically structure a user testing session as follows:

**Introduction and preamble -** introduce the test, what you want to learn generally, and give the participant some guidelines. This also gives them a chance to ask you any burning questions before you begin.

**Tasks -** Observe the participant completing a set of tasks, usually about 3-5 of them.

**General questions and closing -** Any non-usability related things you want to know and a last chance to ask/answer questions.

The industry standard for task-based user testing with a clickable prototype is 5-8 people. You’ll know you have tested enough people when participants start to point out the same issues.

## Example 1: Data export testing plan

### Target User

Test ~5 users who are relatively expert users of the platform already, preferably also folks who match up with our “Data worker” persona e.g. a person who regularly works with data, reviewing, cleaning and/or analysing.

Ideally, we would test this with data workers from partner orgs in DREAMS.

### Preamble

We are improving our data export features so that Ushahidi will be more effective for data work and M&E in your organization. We’d like to get your feedback on some screens of a process where exporting data is important. We want to understand how easy or difficult it is to complete some tasks. I’ll ask you to complete 3 tasks and then ask some general questions at the end, if that’s alright. The test should not take longer than 30 minutes.

Before we start there are a few things I’d like you to keep in mind.

The prototype is clickable, but not fully-functional, so please don’t worry if it doesn’t function the way a normal website would.

You have no need to feel pressure here, we’re testing the designs, not you, so don’t be afraid to explore and speak your mind.

You don’t need to worry about hurting our feelings. We have very thick skin, so don’t be afraid to be brutally honest.

It helps a lot for our analysis if you can think out loud and describe what you’re doing as much as possible while you’re using the prototype. We’re not very good mind readers, and it will help us take notes and understand your thinking as you’re moving through the tasks.

We will be taking notes during the test, but there will be no recording of your voice or any video.

If at any point you choose to stop the session, that’s absolutely fine. Just let me know.

### Tasks

You could lead with a question where you ask the users to describe their experience of working with data and how much importance it has within their processes. May help to get them in the mindset to ‘work with data’.  
Here we may extract some of the more general assumptions that we make as platform workers around ‘data cleaning’ level of comfort of working the data in the platform before moving into the software they may feel more comfortable in \(R studio/Excel/Spreadsheet programs\).

#### Task 1: Locate data export items in the information architecture

* Open a live Ushahidi deployment in production and ask the participant to point out/navigate to areas on the site where they would expect to find the ability to export all of their data. Allow them to explore for about a minute or so for each idea of where things are. They will think as they navigate.
* “Can you tell us why this location came to mind?”
* Ask for another place they might expect to find the ability to export data. Repeat step 2.

#### Task 2: Export data from the filter pane

* Prototype opens on Data view with the filter pane open. Explain that they have filtered to include all posts in a particular order.
* What would you expect to happen if you click on “Export filtered reports”?
* Ask them to click “Export filtered reports”. How does this differ from what you expected? What do you think you have to do now?
* Which of these data formats would you choose and why? Is there a combination of these they would download/export?
* What do you expect will happen when you click “Export now”? Click button.
* How did the result of clicking “export now” differ from your expectations?

#### Task 3: Export data from settings

* Navigate to settings
* Which menu item would you choose in order to export all the data in your deployment? Why?
* Click “Export data”
* What do you think you need to do on this page? Have them complete the flow as they see fit and take notes on pain points.
* What do you think “Select fields” means?
* Click “select fields” and ask if this matches their expectations
* How would you determine which fields to export?
* Have participant complete export and ask for feedback, noting any pain points

#### Task 4: Assess exported file

* Open an example file
* Take a quick look at the exported file. How does this compare to your expectations?
* If no, a quick description why and what would you do next? \(re-export? change filters? export all data\)
* If yes, what would you do next?

#### General Feedback

* What do you think overall?
* If we released this feature tomorrow, how would it impact your work in your organization? If yes/no, ask for more information?
* If you had to teach a colleague how to use these features, how long would it take you to teach them? Why?
* Any final thoughts?

## Example 2: HDX testing plan

Prototype link: [https://adobe.ly/2HecW3O](https://adobe.ly/2HecW3O)

#### Target User

Test ~5 users who are relatively expert users of platform already \(if available\), preferably also folks who match up with our “Data worker” persona. If not available the user testers must be tech savvy enough to understand the concept of ‘tags’ they don’t have to be experts in hxl or any kind of tagging language but they must understand the value of assigning a ‘label’ to a piece of data so that it can be ‘processed’ quicker.  
Users should know what spreadsheets are and be familiar with CSV as a term/file extension name.  
If we can test with someone self-identifying as partially-sighted or dyslexic this would be a good start to adding accessibility into our usertesting.

Ideally we would test this with data workers from partner orgs from COMRADES or organisations that have an interest in HXL and HDX already and have some level of familiarity.  
If they are too familiar we run the risk of the test being about what they specifically want from hxl/hdx and not about the UX of this system.

#### Preamble

We are improving the way that users can ‘tag’ and assign a label to specific kinds of data within an Ushahidi deployment so that the process of extracting meaningful insights from data will be easier.  
We’re particularly interested in how clear the method of ‘tagging’ is within our system.  
We’d like to get your feedback on some pages of a process. I’ll ask you to complete some simple tasks and I’ll be asking questions as you complete these tasks.  
You have no need to feel pressure here, we’re testing the pages, not you, so don’t be afraid to explore and speak your mind. In fact we’d encourage you to be an honest and talkative as possible.  
We’ll have some time for general feedback and questions at the end.  
Please wait until I’ve finished a question before clicking links or buttons if possible.

The test should not take longer than 30-40 minutes.

Before we start there are a few things I’d like you to keep in mind:

The prototype is clickable, but not fully-functional, so please don’t worry if it doesn’t function the way a normal website would. If something doesn’t work, try to explain what you were trying to do and expecting to happen.  
It helps a lot if you can think out loud and describe what you’re doing as much as possible while you’re using the prototype.

We will be taking notes during the test, but there will be no recording of your voice or any video.  
\[My colleague _name_ will also be on the call \(introduce colleague\) to take a duplicate set of notes just in case I miss anything while we’re talking\]

We may ask you to slow down temporarily while we take accurate notes so don’t worry if we ask you to go a little more slowly.

If at any point you choose to pause or stop the session, that’s absolutely fine. Just let me know.

#### Task 1 - Previous experience with ‘tagging’ \(no screen required\)

Q: Ask the users to describe what they think when they here the term ‘tagging’ or ‘labelling’ means in relation to websites, digital products, software etc.

Q: Ask users when they have previously had experience of ‘tagging’. What was the nature of the content? Was there any extraction of data afterwards? Whether there was anything they wish they could have done that they couldn’t do.

Hypothesis, Do not share with user tester: We’re looking for the users to describe either prescriptive tagging or freeform tagging. Do users expect to only be able to tag within a set of constraints or do they expect to write their own and those tags to be very intelligent.

#### Task 2 - Locating in the information architecture \(Prototype needed - [https://adobe.ly/2vmIZJo](https://adobe.ly/2vmIZJo)\)

Firstly, Imagine you work for a charitable organisation and are about download a spreadsheet of information that has been gathered about volunteers. These volunteers were tasked with handing out flyers about an event you’re organising.  
You want to download information from a system that collects feedback from these volunteers. The information that has been collected are things like where they handed out flyers, how many flyers they gave out and the date and time they were handing out flyers.  
I want you to remember that this scenario. If you need a reminder of this, just let me know.

Q: Can you show me where you’d look to download CSV spreadsheet files?

Q: \(On Export page with 3 CTAs\) Without clicking, can you explain to me what options you think are available from here?

Q: What would you need to do in order to feel comfortable choosing one of the three buttons?

Q: Can you tell me what you expect ‘hxl tags or attributes’ to be? Don’t worry if you don’t know, we’re looking for your best guess if you’ve never heard of them before.

Q: If you haven’t already, click a blue, underlined link.

Q: Were you expecting a page like this? If not how does it differ from your expectations?

Q: Spend a minute or so reading the information here. \[Pause\] Can you give me a summary of your understanding of hxl tags. \(There are no wrong answers!\)

Q: How would you get back to where you were?

Q: If you haven't already, please click the back button \(you may need to click a few times on a ‘real’ website.

Q: Imagine you want your volunteer data to include hxl tags.

\(We can briefly explain here that hxl tags are a way of ‘assigning’ a ‘term’ to a piece of information. Use Justin’s analogy of Instagram/Twitter hashtags!\)

Q:Which option would you choose? \(they can click at this point\)

Q: As you choose this option, what are you thinking? What is your first impression of this page? \(Should be on the first view of the export table\)

#### Task 3 - Going through assigning hxl tags in the table

screen required - prototype: [https://adobe.ly/2HecW3O](https://adobe.ly/2HecW3O)

\(You may want to repeat the scenario if tester needs reminding\)

Q: Is it clear how you progress? What are the steps you would take to start this section?

Q: Can you show me how you would know if a certain section was going to be included in the eventual CSV download?

Q: Can you show me how you would add a HXL tag?

Q: \(If they click on the ‘Leave empty’ drop down\) What do you think of the options presented in this drop-down list? What kinds of information does it give you?

Q: Can you add the hxl tag ‘activity’

Q: Can you show me \(without clicking\) how you might add a hxl attribute?

Q: \(If they click on the ‘Leave empty’ attribute drop down\) What do you think of the options presented in this section? What kinds of information does it give you in relation to the tags?  
\(Is the difference between tags and attributes clearer for the user at this point?\)

Q: Can you now show me how you would add a custom hxl attribute? \(Encourage clicking now\)

Q: \(on the ‘please type an attribute’ field\) What do you expect to be able to do here? \(Go through the clicks in order to add the custom attribute\)

Q: Has any information changed? Can you explain what you are seeing/understand?

Q: Can you now add a tag to ‘description’. Can you point to a tag you might choose if you wanted to capture information relating to a ‘flyer’ \(like a handout/leaflet/pamphlet\) There are no wrong answers!

Q:\(If they haven’t already\) Please click ‘item’ and then add a custom attribute for ‘flyer’.

Q: Can you show me how you would add a second attribute? \(Once user find blue link\) Can you now add the attribute ‘adults’

Q: Take a moment to pause, Can you describe your current thoughts on the process? Anything that has stood out to you?

Q: Please continue with the section ‘Location’ and add a tag ‘geo’

Q: \(Pause - see if they notice the list is less populated\) Why do you think this list has less options?  
Q: \(if they don’t notice\) Is there anything you notice about the drop down list? Why do you think this list has less options?

Q: Can you now add two hxl attributes ‘longitude’ and ‘latitude’. \(these are the last tags/attributes to add during the test the ‘export to CSV and HDX buttons should be clickable\)

Q: Take a moment to pause, Can you take a look at this screen and explain what you have just completed.  
Can you point out the HXL tags and attributes?  
Do you remember what they mean? If not can you give a good guess?

Q: Can you now show me how you would get a CSV file? What would you expect to see happen next?

Q: Can you tell me what you think ‘Export to HDX’ means? \(Do people assume this is a file extension type? Do they understand it’s a whole system?\) Go ahead and click ‘Export to HDX’. Take me through your impressions of this message.

General wrap-up questions

#### Current Ushahidi user specific questions:

If we released this feature tomorrow, how would it impact your work in your organization? If yes/no, ask for more information?

#### All users can answer:

Any final thoughts?

