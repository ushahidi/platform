---
description: >-
  Here you will learn how to setup the platform-client repository to work with
  your platform API for development environments.
---

# Setting up the Platform Client

#### What is the platform client?

The web client is the component that end users interact with when opening the Platform website with a web browser. The client interacts with the API in order to perform operations on the system \(i.e. submit posts, query posts\).  


{% hint style="warning" %}
Pre-requisite: Install the platform API by following one of the API setup guides 

{% page-ref page="./" %}
{% endhint %}

{% hint style="warning" %}
Pre-requisite: Install Node V6.x \(you might want to use NVM for this\) before continuing.
{% endhint %}

#### **Getting the platform-client code**

Clone the repository \(this will create a directory named _platform-client\)_

```bash
git clone https://github.com/ushahidi/platform-client.git
```

Go into the platform directory

```bash
cd platform-client
```

Switch to the _develop_ branch

```bash
git checkout develop
```

{% hint style="info" %}
If you haven't used git before or need help with git specific issues, make sure to check out their docs here [https://git-scm.com/doc](https://git-scm.com/doc)
{% endhint %}



#### Install, build and run a local dev server

```text
npm install
```

The client needs to point to the hostname where the backend expects to receive HTTP requests. This has to be set before building the client.

**In order to set up all that, create a file at the location /var/www/platform-client/.env . Use the following contents as an example:**

{% code-tabs %}
{% code-tabs-item title=".ENV" %}
```text
BACKEND_URL=http://192.168.33.110/
PORT=8000
APP_LANGUAGES=en
OAUTH_CLIENT_ID=ushahidiui
OAUTH_CLIENT_SECRET=35e7f0bca957836d05ca0492211b0ac707671261
```
{% endcode-tabs-item %}
{% endcode-tabs %}

To make it easy to call \`gulp\` when building and developing in the app, add **node\_modules/.bin** to your PATH in ~/_.bashrc_. Example PATH \(relevant part in bold\):

export PATH=$HOME/bin:/usr/local/bin:**node\_modules/.bin**:$PATH

Now run:

```text
gulp
```

alternatively, if you haven't setup node\_modules in your PATH, run:

```text
node_modules/gulp/bin/gulp.js
```

This will start the watcher for local development, and any changes you make to the code will be reflected in the application.

