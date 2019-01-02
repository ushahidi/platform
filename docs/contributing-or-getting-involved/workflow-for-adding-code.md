# Add code to Ushahidi

###  Technical Dependencies

You will need these on your development machine

* git
* PHP version 5.6 or greater
* Node.js version 6 or greater

### Workflow for Adding Code <a id="workflow-for-adding-code"></a>

#### Get a github account

Ushahidi code development is happening in [Github](https://github.com/ushahidi). We track all our tasks, both front-end and back-end, in issues connected to the repo [Platform API](https://github.com/ushahidi/platform/issues)

First, [create a github account](https://github.com/join).

#### Fork the repository

A fork is a copy of a repository. Forking a repository allows you to freely experiment with changes without affecting the original project.

The Ushahidi is built from 3 separate repositories. Depending on the task you'll need to [fork](https://help.github.com/articles/fork-a-repo/) one or more of these. Usually you'll need to fork at least the API and the Client repositories.

* [Platform API](https://github.com/ushahidi/platform): This is the where the API for the platform is developed.
* [Platform Client](https://github.com/ushahidi/platform-client): This is where the JS client for the platform is developed.
* [Platform Pattern Library](https://github.com/ushahidi/platform-pattern-library): This is where the designs, HTML and CSS for the platform is developed

To fork a repository:

1. Click the link above
2. In the top-right corner of the page, click Fork.![](https://help.github.com/assets/images/help/repository/fork_button.jpg)

Thats all! Now you have your very own fork of the original repository.

#### Clone your fork

If you hadn't yet cloned \(and installed\) the platform code, you can just go ahead an clone your fork:

```text
git clone git@github.com:yourusername/platform.git
```

#### Add your fork as a remote

If you already cloned and installed the platform, you can add your new fork as a "remote" repository:

```text
git remote rename origin upstream
git remote add origin git@github.com:yourusername/platform.git
```

When you clone a repository, the URL you clone is always created as the "origin" remote repository. The commands above rename the "origin" to "upstream", and create a new "origin" that points to your fork. This will allow you to pull in new versions of the platform, but push your own branches to your fork.

#### Find a feature to work on

The best way to pick a feature to work on is to say hi to Ushahidi’s developers, let them know what you’d like to work on \(front end, back end, etc\), and chat about what could be suitable for you.

Ushahidi issues \(bugs, feature requests, etc\) are in Github Issues. Find something that needs doing.

* [Community tasks](https://github.com/ushahidi/platform/labels/Community%20Task) in github are feature that are up for grabs by community devs.
* Other tasks that we haven’t labeled yet may be suitable for work. Feel free to contact the team if you intend to work on something that grabs your attention.

#### Start a branch for your feature

If you’re working on a feature that nobody has claimed before, you will need to create a branch of Ushahidi that’s specific to this feature. To do this, cd \(change directory\) into your Ushahidi code in the terminal window, and type:

```text
git checkout master
git pull
git checkout -b some-task
```

Where “some-task” is a short description _without spaces_ of what this task is \(e.g. “visualise-data”\). Now you can start work on your code.

#### Write Code

Now write your code. Make sure you meet the [Ushahidi coding standards](https://ushahidi.gitbook.io/platform-developer-documentation/development-process/coding-standards) and use the [Ushahidi pattern library](https://ushahidi.gitbook.io/platform-developer-documentation/getting-started/setting-up-the-pattern-library-for-development) if you’re writing front-end code.

If you get stuck, or want to talk through ideas, you can contact other Ushahidi developers on the [hipchat, IRC or Gitter](https://ushahidi.gitbook.io/platform-developer-documentation/contributing-or-getting-involved/workflow-for-adding-code#join-the-community).

#### Submit your code

When you’re ready to submit your code for approval, do this:

1. Commit and push your code

   ```text
   git add .
   git commit -m “message about this commit”
   git push origin some-task
   ```

2. Then, open your fork on github, ie. _"https://www.github.com/yourusername/platform"_. You’ll see a banner indicating that you’ve recently pushed a new branch, and that you can submit this branch “upstream,” to the original repository:![](https://github-images.s3.amazonaws.com/help/pull_requests/recently_pushed_branch.png)
3. Click on "Compare and Pull Request" to create a pull request. Enter a title and description, then click "Create pull request"![](https://github-images.s3.amazonaws.com/help/pull_requests/pullrequest-send.png)

The first time you submit code you may be asked to sign Ushahidi’s [contributor agreement](https://phabricator.ushahidi.com/L2).

The Ushahidi admins will then review and comment on your code, and will either accept your code or ask you to make changes to it. If you are asked to make changes to your code, make those changes then resubmit your code using:

```text
git add .
git commit -m “message about this commit”
git push origin some-task
```

If your code is accepted, then the admin will merge your pull request. Your code will then appear in the Ushahidi Platform github repository, with you credited for it.

### Further Reading <a id="further-reading"></a>

* [Contributing to open source](https://guides.github.com/activities/contributing-to-open-source/)
* [Forking projects](https://guides.github.com/activities/forking/)

### Join the Community <a id="join-the-community"></a>

Connect with the wider Ushahidi community:

* Join the discussion on [our forum](http://forums.ushahidi.com/)
* Sign up on the [mailing list](http://list.ushahidi.com/)
* Chat with us on:
  * IRC at [\#ushahidi on Freenode](irc://irc.freenode.net/#ushahidi)
  * Gitter at [ushahidi/Community](https://gitter.im/ushahidi/community)
  * Hipchat [Platform v3 room](https://www.hipchat.com/g9I7z8M9a)
  * Messages to any of these channels should show up on all of them!

