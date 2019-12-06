# Development environment setup with Vagrant

### Video-tutorials

The setup in this guide is demonstrated in below video as well if you want to watch and follow the guide at the same time!

{% embed url="https://youtu.be/7ZshCFUM9j0" caption="Setting up Platform backend, recorded in Mac OS" %}

## Installing the API

This guide relies heavily on Vagrant and assumes some previous knowledge of how to use and/or troubleshoot vagrant.

{% hint style="info" %}
If you want to learn more about vagrant, please refer to their docs here [https://www.vagrantup.com/intro/getting-started/index.html](https://www.vagrantup.com/intro/getting-started/index.html)
{% endhint %}

## Prerequisites

{% hint style="danger" %}
Please make sure you install everything in this list before you proceed with the platform setup.
{% endhint %}

* [Vagrant](https://www.vagrantup.com/downloads.html)
* Recommended: [Vagrant host-updater plugin](https://github.com/cogitatio/vagrant-hostsupdater) - this is useful to avoid having to update /etc/hosts by hand
* [VirtualBox](https://www.virtualbox.org/wiki/Downloads) - Note: Windows users may be required to Enable VT-X \(Intel Virtualization Technology\) in the computer's bios settings, disable Hyper-V on program and features page in the control panel, and install the VirtualBox Extension Pack \(installation instructions here.\)
* [Composer](https://getcomposer.org/doc/00-intro.md#system-requirements)
* PHP &gt;=7.0 &lt;7.2 - if you are using Platform V4.0.0
* PHP &gt;=7.1 &lt;7.4 - if you are using Platform V4.1.0 or later

### Getting the API Code

Clone the repository \(this will create a directory named _platform\)_

```bash
git clone https://github.com/ushahidi/platform.git
```

Go into the platform directory

```bash
cd platform
```

Switch to the _develop_ branch

```bash
git checkout develop
```

{% hint style="info" %}
If you haven't used git before or need help with git specific issues, make sure to check out their docs here [https://git-scm.com/doc](https://git-scm.com/doc)
{% endhint %}

### Getting the web server running

Once you have the code, the next step is to prepare a web server. For this part, we will use vagrant, with the Vagrant and Homestead.yml files that ship with Ushahidi.

First up we need to install the PHP dependencies. In the _platform_ directory, run:

```bash
composer install --ignore-platform-reqs
```

{% hint style="info" %}
Without using --ignore-platform-reqs you might run into an error like "The requested PHP extension ... is missing from your system". That's ok. You don't need all the PHP extensions on your _host_ machine, since the vagrant setup already has them.
{% endhint %}

{% hint style="warning" %}
If you get a warning like "In MemcachedConnector.php line 69: Class 'Memcached' not found" at this point you can safely ignore it, we will come back to it later.
{% endhint %}

Bring up the vagrant server. Since this is the first time you run it, it will also provision the machine from scratch:

```bash
vagrant up
```

Our vagrant box is built on top of Laravel's Homestead, a pre-packaged Vagrant box that provides a pre-built development environment. Homestead includes the Nginx web server, PHP 7.1, MySQL, Postgres, Redis, Memcached, Node, and all of the other goodies you might need.

{% hint style="info" %}
If you see an error like "Vagrant was unable to mount VirtualBox shared folders..."
{% endhint %}

* [ ] Verify that Vagrant and VirtualBox are up to date.
* [ ] Verify that the VirtualBox Guest Additions were installed \(and fix it if they weren't\)
  * [ ] `vagrant ssh` \(to ssh into the machine. If you get an error like 'the path to your private key does not exist' when doing `vagrant ssh`, you need to generate a key, or if you already have one, double-check the path in the file "Homestead.yaml" . One good guide on generating keys is found here: [https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent\#generating-a-new-ssh-key](https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent#generating-a-new-ssh-key)\)
  * [ ] `lsmod | grep vboxguest`
  * [ ] If this command doesn't return anything, VB Guest additions are likely not installed correctly. A fix for this is to install the vbguest vagrant plugin.
  * [ ] if you are in the vagrant box, type `exit` and hit the return key to exit it
  * [ ] run `vagrant plugin install vagrant-vbguest`
  * [ ] run `vagrant up --provision` to continue
* [ ] If that doesn't work, and you are in a linux or MacOS environment \(this is not compatible with Windows!\):
* [ ] open the Homestead.yml file
* [ ] Add `type: "nfs"` to the two directory mappings as shown below
* [ ] run `vagrant up`

```text
 -
      map: "./"
      to: /vagrant
      type: "nfs"
  -
      map: "./"
      to: /home/vagrant/Code/platform-api
      type: "nfs"
```

{% hint style="success" %}
Now that you \(hopefully\) have a working vagrant machine, you will have to ssh into it to finish installing the dependencies.
{% endhint %}

```bash
vagrant ssh
```

```bash
cd ~/Code/platform-api
```

```bash
sudo update-alternatives --set php /usr/bin/php7.1
```

```bash
composer install
```

{% hint style="warning" %}
**Important:** If you didn't setup vagrant-hostupdater, or if it failed for any reason, you will need to add the following lines to /etc/hosts in your host machine.
{% endhint %}

```text
192.168.33.110  platform-api
192.168.33.110  api.ushahidi.test
```

At this point you should have a running web server, but your deployment isn't set up yet. We still need to configure the database and run the migrations.

### **Setting up the deployment's database**

* Copy the configuration file `.env.example` to make sure the platform can connect to the database. 

```bash
cp .env.example .env
```

* Run the migrations. This is required to be able to use your deployment, since it includes basic data such as an initial "admin" user, roles, the database schema itself, etc.

```bash
composer migrate
```

* Go to [http://192.168.33.110](http://192.168.33.110/) in your browser to check the API is up and running. You should see some JSON with an API version, endpoints and user info.

Example JSON

```javascript
{"now":"2018-11-06T19:18:23+00:00","version":"3","user":{"id":null,"email":null,"realname":null}}
```

### Installing the client

Congratulations! You have set up the API. You may want now to [build and install the web client](setting-up-the-platform-client.md) for a full experience.

