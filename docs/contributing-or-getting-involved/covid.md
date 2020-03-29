# Specific tasks needed for COVID19-support

Ushahidi is open source software created for the benefit of a large global community improving the world for themselves and others, holding governments accountable, and raising their voices to be more powerful together. Today, we are reaching out to technologists everywhere to ask for your help to make Ushahidi Platform better.

If you have skills that you want to put to work in favor of those who depend on our tools, and some free time \(which we fully acknowledge is a privilege not everyone has\) this is an unprecedented opportunity to do so.

#### Some specific areas where we need help

This are in a rough order of priority, they may change at any time. Check in with our team if you have questions.

* **Write a blog post** about your experience using Ushahidi Platform V3+ and what you learned from it, then share it with us so we can amplify and help others who are setting up their first Ushahidi Platform deployment.
* **Improve our location and map functionality** so that we can [**support polygons as well as points**](https://github.com/ushahidi/platform/issues/1231)**.** You can also work on [**capturing zoom levels and other metadata**](https://github.com/ushahidi/platform/issues/600) for location fields, as described here. Follow up with any questions you may have to collaborate on specs and implementation.
* **Improve the import feature** so that it's faster and easier for new deployers to get started using data they already have. The import feature can be a little unstable at times, and we're looking for help making it better.
* **Get involved in fixing other bugs and improving features:** there are hundreds of bug and feature improvement reports, and while a vast majority of them take a little work and experience to get right, some are actually quite accessible even for beginners. [Follow this link](https://github.com/ushahidi/platform/issues?q=is%3Aopen+is%3Aissue+label%3ABug) if you are looking for an opportunity to squash some bugs for the greater good. Our team will be forever thankful.
* **Implement an USSD integration** that will make it possible to collect data in a more structured fashion than what we currently have in our SMS integration. Tim made amazing progress in his Africa's Talking USSD integration prototype, supported by David Losada, and we would be thankful to anyone who is willing to add support for USSD providers. [https://github.com/ushahidi/platform-api-ussd-service](https://github.com/ushahidi/platform-api-ussd-service)
  * You are also welcome to collaborate and extend the work done by Tim and David with Africa's Talking, as it can always be improved. In particular, it needs some work in terms of field validation, a feature they already worked on and that we deem essential for easy collection of data that truly helps populations at risk with limited resources. ****
  * **Work on a way to setup the USSD integration that allows people without technology background to do it:** collaborate with the Ushahidi team on designing an easy to use setup tool so that others can use USSD in their deployments.



To get started, begin with setting up the Platform-api and Platform-client. Head over [here](../development-and-code/setup_alternatives/) to see our setup-guides.

