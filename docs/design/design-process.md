---
description: >-
  Understanding the process that internal Ushahidi designers take on and how to
  contribute design to Ushahidi OSS products.
---

# 🎨 Design: overview

## Ushahidi Design process

Ushahidi has a design process that aims to **include user voices as the key component for design insight.** As such, all design work relating to a Ushahidi product must have sound user rationale or direct user voices. The ultimate aim of design within Ushahidi is to listen, interpret, facilitate and raise voices of users directly operating the software with the direct responsibility in improving the usability, accessibility, visual design, brand recognition, interaction and purpose of that feature through the medium of design.

Designs and prototypes created in design software tools are by nature not always 100% accurate to the coded experience. The prototyped design should strive to be as accurate to the live coded experience as possible within reasonable time constraints. As such, there is a likelihood that some pixels may shift, some colours may be a hex code out or a form element may have a slight difference. Unless explicitly detailed as a design decision, the patterns existing in the Ushahidi \(and other Ushahidi related properties\) Pattern libraries are to be the true and accurate construction elements of an Ushahidi interface.

[Ushahidi Pattern library](http://preview.ushahidi.com/platform-pattern-library/develop/index.html)

[Working files or 'sticker sheet' of Ushahidi platform UI elements](ushahidi-platform-sticker-sheet.md)

Depending on the complexity of the issue needing design work there are approximately three approaches Ushahidi takes to design.

1. **Dev design**
2. **Light design**
3. **Heavy design**

## Finding Design issues in the Ushahidi backlog

Looks for the labels 'Design' 'UX/UI' and 'Usertesting' 'Design Research'

## Ushahidi issue structure

Every issue that needs design support should have a clear 'user story' to give detailed context as to the problem that design is trying to help solve.

There should be relevant screenshots or access to see what exists already in the product as well as an understanding of the wider design ecosystem available to use.

### 1. Dev design

In certain circumstances, 1st draft of UI or design layout can be done by the development team using existing patterns, brand guidelines, and relevant frameworks. This allows for an agile approach to development where a feature or product needs to be live and useable quickly.

This 'dev design' will be able to gather live user feedback and be a 'living testable product'. Design is then responsible for monitoring, tracking and developing any design changes that would improve this 'dev design' for users. Where able, producing a user test script \(link to example\) and conducting in-person or remote user testing on the 'dev design' will help to move the feature/product forward in usability.

The developers at Ushahidi should be empowered to make appropriate design choices when working on a feature. For example, if design dictates a certain layout for a form element and the existing pattern for the form element is slightly different, unless the change to the element is trivial in terms of dev time or the specific design has usability/accessibility impact using the existing pattern for the element is acceptable.

Developers should be able to find everything they need in the [Ushahidi Pattern library](http://preview.ushahidi.com/platform-pattern-library/develop/index.html).

![This design has been re-used by devs for various screens for messages.](../.gitbook/assets/pattern-library-snackbar%20%281%29.png)

![Ionic has &apos;out of the box&apos; UI that works for a &apos;dev design&apos;](../.gitbook/assets/ionic%20%281%29.png)

### 2. Light design

'Light design' describes a feature or issue which is a small element, interaction or UX challenge. Typically not part of a larger series of screen flows these design tasks should not take an extended period of time to complete and can typically be completed using design knowledge, existing user behaviour knowledge and good design practice

[See Ushahidi best design practice here](best-practice-design.md)

'Light design' may or may not include user testing. Usertesting should be completed if there is a strong difference in design opinion on how to execute the design. User testing and insight should be impartial and test as accurately as possible for a user-initiated solution.

Light design can also benefit from live testing or in the future A/B testing.

### 3. Heavy design

'Heavy designs' are epics, features, issues, and projects that either has no previous basis for design e.g. a completely new product, [Intellectual Property](https://en.wikipedia.org/wiki/Intellectual_property) or campaign. or a fundamental rethink and redesign of an existing feature or product. These are often features, issues or projects that span or affect multiple parts of a Ushahidi product. e.g. A new data export integration that operates in fundamentally different ways to the existing data export methods and requires analysis of existing ways that users interact with data export as well as the new way of exporting data. \([See HDX epic](https://github.com/ushahidi/platform/issues/2397) and the [subsequent design prototype](https://xd.adobe.com/view/2690f082-d88d-4788-5db7-b04c9474a404-50a1/?fullscreen)\)

We 'heavy design', often our intention is to design within the limits of existing patterns and where appropriate and achievable, leave the usability, UI, and interactions better than they were previously.

'Heavy design' will need to include user testing and user validation/feedback. The minimum requirement for user testing to observe meaningful results and mitigate bias is 5 user testers.

'Heavy design' will often take longer time-wise, approximately no less than 3 days and sometimes up to 1 month inclusive of Ushahidi team feedback, demos and user testing with appropriate revisions.

[Usertesting process](user-testing-process.md)

[Creating user testing scripts](user-testing-script-examples.md)

[Synthesising the results of user testing examples](synthesising-user-testing-results-examples/)

'Heavy design' should be more conscious of the impact on development. There should be any relevant supplementary information in order to develop such as colour codes, sizes, grid systems, responsive screen size behaviour, interaction animation or examples.

## Design + Development Collaboration and 'handover' for development

The collaboration between design and development should be seamless and inclusive of all parts of the process. Communication and rationale is key in design decision making.

The designer/s should be able to provide a screen by screen visual description of the interactions they intend \(or have observed and design for\) users to make. Preferably in a widely sharable format that doesn't require any non-standard download of software to view files. The prototypes should show exact click/hit areas that users will use in order for the user journey to be understandable.

Errors, mistakes, validation, accessibility and relevancy should all be considered when producing design.

It is one of design's responsibilities to **advocate for users** through facilitating their product needs and **subsequently visualising and communicating these needs clearly**.

{% hint style="warning" %}
#### Design can't solve everything at once

Design has to be 'bounded', well defined and taken as an iterative, incremental improvement approach. Design is never 'done' and it is rarely possible to create 'entire' design solutions. The designer and the team are responsible early on in a design process decide what will be completed and what is out of scope for that design iteration and to be worked into the next one
{% endhint %}

