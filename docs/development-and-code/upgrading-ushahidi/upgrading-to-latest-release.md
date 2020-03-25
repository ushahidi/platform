# Upgrading to latest release

## Updating your deployment to the latest version <a id="updating-your-deployment-to-the-latest-version"></a>

Instructions on how to upgrade your Ushahidi deployment.

1. Make a backup copy of the current folder where you have installed the Ushahidi Platform.
2. Make a backup copy of your database.
   1. The exact procedure to do this depends on your environment.
3. Download the latest .tar.gz file from our [github releases page](https://github.com/ushahidi/platform-release/releases) . Please note that pre-releases are not considered stable and you may find issues with them.
4. Uncompress the downloaded file on the same location where the Ushahidi Platform is currently installed.
5. If you had made any changes to .htaccess files, application config files or similar after installation, restore those from your backup copy.
6. Re-run some of the installation steps \(refer to the [installation guide](https://ushahidi.gitbook.io/platform-developer-documentation/v/master/getting-started/setup_alternatives/installing-for-production-environments) for more detailed instructions\). In particular, re-run these two steps
   1. Running database migrations
   2. Ensuring that log, cache and media/uploads under platform/application are owned by the proper user

## Updating the client \(for developers\) <a id="updating-the-client-for-developers"></a>

From your local repository fetch the latest code and run \`npm install\` to update your modules:

```text
git pull
```

```text
npm install
```

```text
gulp build
```

The updated version should load when you reload your browser.

## Updating the API \(for developers\) <a id="updating-the-api-for-developers"></a>

From your local repository fetch the latest code and run \`bin/update\` or \`bin/update --production\` if you are running on a production environment:

```text
git pull
```

```text
bin/update
```

OR

```text
bin/update --production
```

