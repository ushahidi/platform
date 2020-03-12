---
description: Keep your community protected
---

# Security for deployment admins

As an administrator for a Ushahidi Platform deployment, there are a few basic things to keep in mind, in order to reduce the possibility of your deployment putting members of your community at risk.

## Understand the risks

Each Ushahidi Platform deployment is fairly unique in the sense that it is created for a specific problem in the context of a specific community.

These circumstances may \(or may not\) make the exposure of information a risk for members of the community that you are receiving reports from, your team, or both.

Take for instance a deployment collecting information about crime. If somehow the criminals got information that allowed them to identify who has reported their activities, those persons would be in danger. If on top of that, your team attempts to take some action like approaching the area to verify the report, criminals may be on the know and put your team in danger, additionally.

Of course this example may sound extreme for your particular case, after all, crime is one of the most sensitive and dangerous activities we could have picked.

In any case, we encourage you to go through the mental exercise of asking yourself at least these simple questions:

* Are all the data I'm collecting ok to show in public? Which data items are not?
* Could any of the data I'm collecting and displaying be used \(in isolation, or when aggregated and analysed\) to identify the individuals that reported it? Who could be interested in doing that and what may their motivations be?
* Am I knowledgeable of the risks that affect the data while it's in transit through the different data sources I intend to configure?

## Privacy settings in the surveys

Please do keep in mind that the survey configuration in Ushahidi Platform allows fairly fine- grained configuration, regarding which survey questions have their answers available publicly, and which others are only available to your team \(and within your team, specific roles\)

Make sure to configure your surveys accordingly, and that you review this periodically.

### Personally Identifiable Information

Personally Identifiable Information \(or PII\) is information that is not very sensitive by itself, but if analysed, could be used to mount or assist a process leading to knowing the identity of the person submitting the report.

Some very direct examples of PII items are telephone numbers and e-mail addresses. Having access to those and some additional database, could easily lead to identities of persons being identified.

One other example is the combination of location data and date of the report. In an urban setting with surveillance, someone with access to the surveillance and both data items could identify who issued a report.

In general, the more data items an attacker may collect that bear relationship with a person's traits, possessions or circumstances, the more likely the attacker is to successfully break anonymity.

The Ushahidi Platform offers settings to protect some of these information items, without completely hiding them, by making them fuzzier. This is important for data that is valuable to show aggregated \(i.e. incidents in a geographic area\), and thus is not desired to make completely private.

## Secure and non-secure data sources

Not all the data sources that can feed data into a Platform deployment have the same security characteristics.

Even if your surveys and your server are well protected, your adversaries may be tapping on your data while it's being sent to you. Here are a few words about the different data sources:

### GSM protocols: SMS or USSD

These protocols don't make much of an effort to apply encryption to the information sent over them. The barrier to be able to collect GSM data has always been laid down in terms of access: being able to access the GSM network management hubs, or having the sophisticated equipment to gather data "from the air" locally.

Many governments will most definitely be able to leverage their power in order to obtain access. And, unfortunately, individuals may nowadays acquire relatively affordable equipment to capture this data "from the air".

The consensus is that these are not data sources that can be relied on for keeping secrecy, and thus, shouldn't be used in high-stakes risky situations.

### Web and mobile application

This is currently the sort of data source with the highest potential to be safeguard the secrecy of your data while in transit.

If correctly configured, a deployment may require all its incoming Internet traffic to use a recent version of a data security protocol such as TLS . The version matters too, as time goes, vulnerabilities are found on older versions and fixed in newer ones.

Make sure to consult with your hosting and systems person, to ensure things are properly set up, **and maintained**.

In case you chose to keep your deployment at [https://ushahidi.io](https://ushahidi.io/create) , Ushahidi will be taking taking care of this for you.

### E-mail

The kind of secrecy that you may get with e-mail is in most cases hard to predict.

There are protocols that allow e-mail information exchange to happen inside the same security protocols that protect media traffic \(i.e. TLS\) , but there is generally no way for you or reporters to be sure that every email is going to be encrypted from end to end.

For this reason, we wouldn't openly recommend e-mail if security is of concern.

### Social media

All social media currently supported in Platform \(i.e. twitter\) is public, and therefore not very secret.

## Read in the team

If you've got a team working with you, it is wise to let them know about your risk assessment and the measures you have decided to take.

A team's security is only as strong as its weakest link, and keeping someone in the dark about the security situation is the easiest way to create that weak link.

