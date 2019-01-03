# Frequently Asked Questions

## How  can I join the Ushahidi Community?

Connect with the wider Ushahidi community:

* Join the discussion on [our forum](http://forums.ushahidi.com/)
* Sign up on the [mailing list](http://list.ushahidi.com/)
* Chat with us on:
  * IRC at [\#ushahidi on Freenode](http://irc//irc.freenode.net/#ushahidi)
  * Gitter at [ushahidi/Community](https://gitter.im/ushahidi/community)
  * [Skype](https://join.skype.com/S9t68IVKzwo8)
* Messages to any of these channels should show up on all of them!

## How can I start contributing code to the platform?

TO contribute code to the Ushahidi platform, please follow the guidelines here: [step-by-step guide to adding code to the platform](https://www.ushahidi.com/support/add-code-to-ushahidi)

## How can I contribute to translations?

You can help us translate the platform into as many languages as possible allowing everyone access. Please follow the instructions to start translating here: [Instructions on how to start translating](https://wiki.ushahidi.com/display/WIKI/Localization+and+Translation+-+How+to)

## All I do is get an ugly page with text that looks like code. What do I do?

You have installed only the API. Unless you are installing the bundle that contains both client and API, you would need to set up both separately. This also requires you to set up two different virtual hosts under different subdomains or ports. If this sounds too complicated, please look at the simplified install of the bundle.

## When I open a URL with a path other than / \(i.e. /views/map \) I get a "404 not found error". 

Your web server setup is not handling the URLs as designed. There can be different reasons for this. If you are using Apache, please make sure that you have the directive “AllowOverride All” configured for the folder where you made your installation. If you are using nginx, please make sure that you are using our recommended configuration file.

## Do you support Windows installations?

We have done some tests with that, our documentation has some instructions on how to go about this.

## I am getting some sort of PHP error

Please do ensure that you are using a supported version of PHP for the version of platform that you are running. v2 supports up to PHP 5.4 , v3 supports PHP 5.6 and 7.0 , v4 supports PHP 7.0 and 7.1    


## I’m getting a database connection error

 Please verify that you have created your MySQL database, know the correct credentials for the API to connect to it and that your “.env” file is created with the expected format and at the expected location  


## Can I install an API?

Yes you can. You can follow this link for more resources on how to install [here](https://ushahidi.gitbook.io/platform-developer-documentation/~/edit/drafts/-LU01pd_YH9N3j-BhwFw/tech-stack-overview/api-documentation).

## How do I install Ushahidi?

Please follow the step by step guide on how to install Ushahidi [here](https://www.ushahidi.com/support/install-ushahidi).

## How do I upgrade Ushahidi 

Please follow the step by step guide on how to update your deployment to the latest Ushahidi version [here](https://www.ushahidi.com/support/upgrading-ushahidi).

## How do I connect to the Ushahidi mobile application

Please follow the step by step guide on how to connect to the Ushahidi mobile application [here](https://www.ushahidi.com/support/connecting-to-ushahidi-mobile-app).

