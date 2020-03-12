---
description: V3+ refers to V3.x.x and later versions (ie V4.x.x)
---

# V2-V3+ Migration tool

## First version - CLI only

The initial version of the migration tool will be a CLI. It will be a tool that can be used with some level of technical knowledge \(ie: uploading a database dump\).

### Evaluating user interest

After launching the first version, which we are committed to doing already, we will gather level of interest, do more research, and then re-bet if there is an appetite for it and strong interest of deployments who actually try and use it.

We will be offering 10 courtesy migrations to Crowdmap SaaS or self-hosted deployments to enable us to understand the limitations of the tool with a wide array of datasets and to be able to gather how much real interest there is in migrating to V3+.

### Tool requirements

* Pre requisite: access to a v2/Crowdmap MySQL Dump that can be loaded into a local database that the migration tool will use.
* Being able to migrate all deployment data for existing features.
* Holding the data that was not migrated in the deployment
* Logging the executed migrations with the intention of:
  * Knowing which migration was executed and when.
  * Being able to migrate old data from currently non-existing features when/if we add those old features back in V3+ in the future.
* It should be easy to understand which data was migrated and which data was not \(ie: tables/fields\) from looking at either the migration logs table, or the output of the migration CLI.
* It should not truncate data.
* The migration matching logic should be documented.
  * It could available in the migration tool itself by running  a command like `migration info`
    * This command would output a match table.
  * A matching table should be available in docs.ushahidi.com somewhere. 
* Import media
  * Notes: it may make sense to do a queue to import media.
* Compatibility with usernames instead of emails - forcing users to change their username to an email when they first log in after the migration. 
  * Note: check if the admin/admin thing that tells you to change your email to an email could be extended for ensuring users without emails switch to emails.
  * [https://github.com/ushahidi/platform/issues/703](https://github.com/ushahidi/platform/issues/703)

### Out of scope

* Implementing plugins functionality.
* Implementing new functionality in V3+ to cover missing V2 features.
* New Translations-backend work
* New comments system
* Claim system, Crowdmap ID 
* Old V2 versions. A deployment should be in the latest V2 to be migrated.

