# \[Client\] Setting up the Platform Client for development

## What is the platform client?

The web client is the component that end users interact with when opening the Platform website with a web browser. The client interacts with the API in order to perform operations on the system \(i.e. submit posts, query posts\).

## Video-tutorials

The setup in this guide is demonstrated in below videos as well if you want to watch and follow the guide at the same time!

{% embed url="https://www.youtube.com/watch?v=-GZBJtUQqoQ&feature=youtu.be" caption="Setting up Ushahidi client for local development, recorded in MacOS." %}

{% embed url="https://youtu.be/jPa4oB4XPZw" caption="Setting up Ushahidi client for local development, recorded in Linux." %}

{% embed url="https://www.youtube.com/watch?v=zY80QpptKk0&feature=youtu.be" caption="Download the Platform code with Github-desktop, recorded in Windows" %}

## Installation steps

{% hint style="warning" %}

Pre-requisite: Install the platform API by following one of the API setup guides

{% page-ref page="./" %}

{% hint style="warning" %}
Pre-requisite: Install Node V10.x or higher \(you might want to use NVM for this\) before continuing.
{% endhint %}

### **Getting the platform-client code**

* In a terminal window or command prompt, clone the repository.

```bash
git clone https://github.com/ushahidi/platform-client.git
```

{% hint style="success" %}
Mind your directories. The command above will create a directory named _platform-client_ in the current active directory of your terminal/command prompt. Make sure you know on which directory you are setting **before** running the command.
{% endhint %}

* Go into the platform directory

```bash
cd platform-client
```

* Ensure you are in the _develop_ branch, with the latest, bleeding edge, code.

```bash
git checkout develop
```

{% hint style="info" %}
Alternatively you may run the command

```bash
git checkout master
```

for working on the `master`branch, with more stable code.
{% endhint %}

{% hint style="info" %}
If you haven't used git before or need help with git specific issues, make sure to check out their docs here [https://git-scm.com/doc](https://git-scm.com/doc)
{% endhint %}

### Installing dependencies

* Install the platform-client dependencies.

```text
npm install
```

### Configuring the client build

There are a few quite important variables that are looked at the point when the client code is built into a browser web app. These variables are picked up from a file named `.env` , located in the `platform-client` folder that you have recently cloned from github.

* Create a file named `.env` in your `platform-client` folder.  This `.env` file is required and it doesn't exist by default. Therefore, you must create it. In the following sections we'll let you know about the contents that you should put in that file.

{% hint style="info" %}
In Windows environments, you may find yourself struggling to create this file with the right name. This may be because your text editor insists on appending ".txt" or because it is confused by the leading dot.

In that case, an easy way to create the file is by running the following command in the command prompt, when inside your `platform-client` folder:

```text
type nul > .env
```

This will create an empty file, but with the right name. File Explorer may present this file as a file with empty name and of type "ENV file".

Mac/Linux users may use the `touch .env` command to the same end.
{% endhint %}

### Required build configuration variables

There is only one required variable that must be defined in your `.env` file, and its name is `BACKEND_URL`. Its purpose is to configure the client with the URL to use, in order to send HTTP network requests to the Platform API. If this variable is wrong, nothing works. This variable usually takes different values for different users.

As such, the minimal working `.env` file consists of just this variable.

* In your `.env` file write the `BACKEND_URL` variable, corresponding to your Platform API URL address. This is an example, showing the format used, \(**don't** just copy & paste it to your file!\):

{% code title=".env" %}
```bash
BACKEND_URL=http://dont.copy.this.name.com
```
{% endcode %}

{% hint style="info" %}
If you have used one of our guides for setting up the API locally, check back the relevant section in that guide. Here are some direct links that should take you back there.

* XAMPP setup, click [here](xampp.md#installation)
* Vagrant setup, click TODO
{% endhint %}

{% hint style="success" %}
Take a minute here to make sure you have entered the proper URL and the API is working.

From your `.env` file, copy the variable value \(the part starting with "[http://"\](http://"\)\) and paste it in your browser's address bar, then hit enter. As a result you should see something similar to this:

```text
{"now":"2019-02-04T10:52:25+00:00","version":"3","user":{"id":null,"email":null,"realname":null}}
```

If you get an error, please make sure that your API server is up \(i.e. Apache and MySQL\), and go back to your Platform API installation notes to make sure you had the correct URL.
{% endhint %}

### Advanced: other configuration variables

All the other variables are often not required to specify, as they have sensible defaults.

{% hint style="danger" %}
You can **safely skip this section** if it's your first time setting up the client, and you just want to get it done. Getting into these details may be too much work for a first run.
{% endhint %}

* The `PORT` variable specifies at which port the local development server should listen. The default for this variable is `3000`.
* `TX_USERNAME` and `TX_PASSWORD`  are variables for configuring the credentials to the [Transifex](https://www.transifex.com/) service, which stores multi-lingual versions of the Platform client text displayed on the screen. These are only required if you are going to develop on languages other than English.
* `APP_LANGUAGES` is a list of language codes \(in ISO-639-1 format\) to download from Transifex. For example `APP_LANGUAGES=sw,en,es` would enable the client to appear in Swahili, English and Spanish.
* `OAUTH_CLIENT_ID` and `OAUTH_CLIENT_SECRET` are variables used during the process of authentication of a user against the API. You can ignore these 99% of the times. Also, these are not particularly secret nor provide much security. They just have to exist, and they do by default. \(If  you must know, their values default to `ushahidiui` and `35e7f0bca957836d05ca0492211b0ac707671261` respectively\)

### Making \`gulp\` command available

The `gulp` command, although a bit funny-sounding, is key for all development tasks on the platform client.

By default, this command is hidden within the `node_modules/.bin` directory of your platform-client folder. This makes it a bit awkward to invoke, see these examples:

```bash
# Windows users would run:
node_modules\.bin\gulp
# Mac/Linux users:
node_modules/.bin/gulp
```

That's too much typing.

To make it easy to call `gulp` when building and developing in the app, there are a couple approaches:

* On any operating system, you can choose to install `gulp` globally. You would do it with his command:

```bash
npm install -g gulp
```

* Alternatively, on Linux and Mac, you may edit the `.bashrc` file in your home directory, and append the following line:

{% code title=".bashrc" %}
```bash
export PATH=$HOME/bin:/usr/local/bin:node_modules/.bin:$PATH
```
{% endcode %}

## Running a local development server

The local development server is a web server that makes the platform client available to your browser locally. Additionally, it will watch the `platform-client` folder for changes, and rebuild the application as needed.

* Just run `gulp`:

```text
gulp
```

* And then wait until you see this message in the screen:

```text
webpack: Compiled successfully.
```

At that point the client should be available to the browser on the address [http://localhost:3000](http://localhost:3000) \(unless you specified a `PORT` on your `.env` file\).

{% hint style="success" %}
You are all set for developing, happy hacking!
{% endhint %}

## Appendix: Building for publication

Sometimes you want to host your Platform instance so that other devices on the network or the internet can access it.

For the Platform client this means placing the application files in a disk location configured as a static site, where your web server can find them and send them to those other devices.

In order to build the files for publication, run:

```bash
gulp build
```

This will start the process of generating the static site. Once the files are generated, you will find the files in the **server/www** directory. Depending on your work flow, you may copy these files to your server, or you may choose to point your web server directly to this directory.

In the **server** directory you will also find an example nginx and an example apache2 file to help you with some of the web server configurations.

Please note that you will also need to publish the Platform API, so those other devices can actually make any use of the Platform.

