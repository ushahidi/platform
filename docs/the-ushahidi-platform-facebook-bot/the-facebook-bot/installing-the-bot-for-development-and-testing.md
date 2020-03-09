# Installing the bot

## **Facebook Bot Setup**

The Ushahidi facebook-bot is built with Lumen and a mysql database. To set it up you need to

### **Prerequisites**

1. Make sure you have the following installed:
2. 1. [Composer](https://getcomposer.org/)
   2. [Vagrant](https://www.vagrantup.com/)
   3. [Virtual box](https://www.virtualbox.org/wiki/Downloads)
3. You must have an instance of the COMRADES Platform deployed and accessible via the internet, as you will need to point the facebook bot to your deployment.

### **Set-up**

* Clone the repo: [https://github.com/ushahidi/platform-facebook-bot](https://github.com/ushahidi/platform-facebook-bot)
* Create a .env file. An example-file is found in .env.example

#### **The script**

Bot uses a predefined script when talking to the users. It is not possible to adjust the questions themselves without doing some coding, but during setup, some information about the ushahidi-deployment and the campaign is needed. That information is added in the .env file and is used to create and adjust this script to the organisation using the bot. These are:

**TITLE:** The title of the Ushahidi-deployment connected to the the bot is used

**AIM:** The aim/a short description of the campign where the bot is used.

**NAME:** The name of the bot

**TWITTER\_NAME:** The twitter-name for the organisation/campaign using the bot

**TWITTER\_HASH:** The hashtag used for the organisation/campaign

The full script of the bot can be found [here](the-bot-script.md).

#### **Credentials**

For some of the credentials in the .env-file you need to create a “Facebook-app”. You do that here: [https://developers.facebook.com](https://developers.facebook.com/).

![](https://lh4.googleusercontent.com/lgGT8IVbhxxdST2OP_JtHLYasTJxcJJxUCboxyvHf2euVA_J2OgN-_hyHf9QVfrsDKaa_bd0iVn2Wio-Q1wFSynYx62TMYY0bWfeljpOoTu5_C73hg4VYU-NG1BKjMmVEgjFXp1B)

After creating the app, select “Messenger” under “Add a product” in your app-settings:

![](https://lh6.googleusercontent.com/VhmAqtEXt_s3YvqPq2Ikd3TYJqeEij8J6io73Kr-KI-bLDVcW0duOT7gYYBQlXIUZhkoR1pbGYNIOVDx42bsZYXYugrO3nzndzvaaVEMgEuypS-vhcZCj14ydk6pNegT3uQ4CDqZ)

In order to get an access-token, you need to connect it to a facebook-page. If you don’t have one already, go to the “normal” facebook \(not the developer-one\) and create a page:

![](https://lh5.googleusercontent.com/pxawAZDOjNX-pva0xcwMz_i5XWYs3gEtAOjZbII_gVKzgX7lSFPBC1hjXpkni4vpKbq26I3ly2T7rGBnVemsaqQn1Gp06ZPQHtW-v5L330bfhowNfyVEtc2GsJNVxNiegnNld9Gx)

After that, go back to developer-facebook and your app, in the messenger-settings, you can select your page and generate an access-token:

![](https://lh5.googleusercontent.com/j2rsGdFehBIq3lKsqvhU3_Nx_d4mMpaEl5DR7e_bdQw-Mrrd3ru2rupbiMhcJmurNs6S0VupgWnDvEkO0N-nR3atgrtW44cNdusBerwTwEl1KSVUzuS3EI-uE09mqsmzX86behv5)

Add the access-token to your .env file together with a verify-token of your choice and the facebook secret token found here:

![](https://lh6.googleusercontent.com/u8y4BsUeC3T9-gGZE_L5mIr2s19aMoX6mnJbgkqgedZDv3xFDIiKk2DIVk2EUhgkxz0GcPmtqdNR7u5ktI8cQ9anLkw5wmVQs5raNKbJfDdbJ7Oh6Wc9Vt_FMzxp2K-aaEBMf_0U)

When all those credentials are in your .env-file, together with the credentials for the Ushahidi-deployment you want to use to send reports with, you start the application with

* vagrant up and then
* artisan php migrate --seed \(this sets together the script for the bot, using the variables you added to the .env-file\)

Now go back to developers.facebook.com and click on Messenger-&gt;settings to set up your webhooks.

![](https://lh5.googleusercontent.com/0hhF67lw2vekRMMVcHKbEyZ_8umORCu1OVPtaFNWcvucW8nv8zvJR3cGFtMDL-Hh8g2sw2evrXBJC4c0MqTle3ZUzvm7X3ZYjE1WZOrylyAReJB1R16wudwl3xS_uky62hizCMgc)

Use _your-url/webhook_ as callback-url and verify-token is the same as you choose in the .env file. Select messages and messaging\_postbacks. Click on verify and save.

Go to your page and send a message to the page to start talking to the bot. It is good to add a start-button and a persistent-menu, instructions for those could be found in the facebook-documentation:

[https://developers.facebook.com/docs/messenger-platform/reference/messenger-profile-api/get-started-button](https://developers.facebook.com/docs/messenger-platform/reference/messenger-profile-api/get-started-button)

[https://developers.facebook.com/docs/messenger-platform/send-messages/persistent-menu](https://developers.facebook.com/docs/messenger-platform/send-messages/persistent-menu)

