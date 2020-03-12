---
description: >-
  The script the bot is using is stored in the bot-database. The script is set,
  but you can customise some parts of it to make it fit well with your
  deployment and organisation.
---

# The bot script

If you feel adventurous you can go to the file [database/seeds/**AnswersTableSeeder.php**](https://github.com/ushahidi/platform-facebook-bot/blob/master/database/seeds/AnswersTableSeeder.php) **before** running the database seeds and migrations and change the wording of the questions. Please note, removing questions might cause the bot to break. You can change the wording of the questions, but not removing them.

## The script

**When the user start talking to the bot:**

* Hello! Welcome to {TITLE}
* {NAME}  is creating an open‐source resilience platform help communities reconnect, respond to, and recover from crisis situations.
* Help us test the platform by sending us a report!
* \[Send a report \] ← this is a button the user can click on

**When the user clicks “Send a report”:**

* In a few words, describe what you would like to report.

**After the user writes a description he/she gets prompted with:**

* Anything else? If not, do you want to add a location or image to your report?

\[Send my report\] ← this is a button the user can click on

\[Add an image\] ← this is a button the user can click on

\[Add a location\] ← this is a button the user can click on

**User selects option “add an image”:**

* Ok great! Just add a photo here like a normal chat, and I'll attach it to your report

**The user uploads it and the bot replies with:**

* Ok, got it. Do you want to add a location to your report?
* \[Send my report\]  ← this is a button the user can click on
* \[Add a location\]  ← this is a button the user can click on

**Or \(if location is previously added\):**

* Perfect! Do you want to send your report to us?
* \[Send my report\]  ← this is a button the user can click on
* \[Add an image\]  ← this is a button the user can click on

**User selects option “add a location”**

* Please add your location below

**User selects location on a map**

* Ok, got it. Do you want to add an image to your report?

**Or \( if image is previously added\) :**

* Perfect! Do you want to send your report to us?

**User selects option “Send my report**

* Your report has been saved. Nice work!
* A moderator will check your report before it is published to {NAME}
* What do you want to do next?

  \[Send another report \] ← this is a button the user can click on \(report-flow starts again\)

\[Go to {NAME}\]← this is a button the user can click on \(link to the deployment\)

