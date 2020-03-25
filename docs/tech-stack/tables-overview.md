---
description: This is a list of all tables used for the Ushahidi Platform.
---

# Database \| Tables overview

<table>
  <thead>
    <tr>
      <th style="text-align:left">Table</th>
      <th style="text-align:left">Description</th>
      <th style="text-align:left">Relevant areas of Ushahidi UI/functionality</th>
      <th style="text-align:left">Comments</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="text-align:left">apikeys</td>
      <td style="text-align:left">Generate API keys for external services that connect with the platform
        API</td>
      <td style="text-align:left">Setting -&gt; General (it shows your API Key).</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">config</td>
      <td style="text-align:left">Stores configuration for the deployment</td>
      <td style="text-align:left">-</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">contacts</td>
      <td style="text-align:left">Stores contact information for users</td>
      <td style="text-align:left">Used for: SMS contacts, twitter contacts , mail contacts. Messages are
        usually associated with contacts.</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">country_codes</td>
      <td style="text-align:left">Stores countrycodes for targeted surveys</td>
      <td style="text-align:left">Settings -&gt; Surveys -&gt; Create targeted survey</td>
      <td style="text-align:left">This feature needs the targeted survey feature-flag to be turned on.</td>
    </tr>
    <tr>
      <td style="text-align:left">csv</td>
      <td style="text-align:left">Stores details about a CSV export.</td>
      <td style="text-align:left">
        <p>Used by the UI to check on CSV export status (done, in progress ,etc)
          and get the url to download the file generated.</p>
        <p>
          <br />Settings -&gt; Export data</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">export_batches</td>
      <td style="text-align:left">Used to separate a large export in chunks to be processed.</td>
      <td style="text-align:left">Settings -&gt; Export data</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">export_job</td>
      <td style="text-align:left">Used to store data about the exports requested by the user in the csv
        endpoint.</td>
      <td style="text-align:left">Settings-&gt;Export data</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">form_attribute_hxl_attribute_tag</td>
      <td style="text-align:left">
        <p>Stores the relationship between an HXL tag and attribute and a form attribute.
          For instance the match of a</p>
        <p>#meta tag</p>
        <p>+value attribute</p>
        <p>with a &quot;description&quot; field</p>
      </td>
      <td style="text-align:left">Settings-&gt; Export data</td>
      <td style="text-align:left">HDX Feature - available only in some deployments through the Export UI</td>
    </tr>
    <tr>
      <td style="text-align:left">form_attributes</td>
      <td style="text-align:left">Stores information about each field (question) in a survey.</td>
      <td style="text-align:left">Settings -&gt; Surveys -&gt; Create/Edit survey</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">form_roles</td>
      <td style="text-align:left">Stores information if a survey is restricted to specific roles only.</td>
      <td
      style="text-align:left">Settings -&gt; Surveys -&gt; Create/Edit survey -&gt; Configuration-
        <br
        />
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">form_stages</td>
      <td style="text-align:left">Stores information about each section in a survey, for example post or
        tasks.</td>
      <td style="text-align:left">
        <p>Settings</p>
        <p>-&gt; Surveys -&gt; Create/Edit survey</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">form_stages_posts</td>
      <td style="text-align:left">Stores relations between posts and forms_stages and if a task is completed
        in a post.</td>
      <td style="text-align:left">
        <p>Settings -&gt; Surveys -&gt; Create/Edit survey</p>
        <p>Add post</p>
        <p>Edit post</p>
        <p>View post</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">forms</td>
      <td style="text-align:left">Stores basic information about each survey</td>
      <td style="text-align:left">
        <p>Settings -&gt; Surveys</p>
        <p>
          <br />Add post
          <br />
        </p>
        <p>View post</p>
        <p>Edit/Structure post</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">hxl_attribute_type_tag</td>
      <td style="text-align:left">Stores the types of attributes that can be matched with each HXL attribute.
        For instance you can only match #geo (+lat + lon) to location fields.</td>
      <td
      style="text-align:left">Settings -&gt; Export data</td>
        <td style="text-align:left">
          <p>HDX feature - only available in some deployments through export UI.</p>
          <p>Uses this field to show the list of available tags for each field</p>
        </td>
    </tr>
    <tr>
      <td style="text-align:left">hxl_attributes</td>
      <td style="text-align:left">All the available HXL attributes</td>
      <td style="text-align:left">Settings -&gt; Export data</td>
      <td style="text-align:left">HDX feature only available in some deployments.</td>
    </tr>
    <tr>
      <td style="text-align:left">hxl_license</td>
      <td style="text-align:left">All the available HDX licenses</td>
      <td style="text-align:left">Settings -&gt; Export data</td>
      <td style="text-align:left">HDX feature only available in some deployments.</td>
    </tr>
    <tr>
      <td style="text-align:left">hxl_meta_data</td>
      <td style="text-align:left">Information required to save a dataset into HDX (humdata.org) such as
        license selected, name, and privacy settings</td>
      <td style="text-align:left">Settings -&gt; Export data</td>
      <td style="text-align:left">HDX feature only available in some deployments.</td>
    </tr>
    <tr>
      <td style="text-align:left">hxl_tag_attributes</td>
      <td style="text-align:left">Relationship between HXL Tags and Attributes (which attributes are available
        for each tag)</td>
      <td style="text-align:left">
        <p>This field is used to show the correct attributes when you select a tag</p>
        <p>
          <br />Settings -&gt; Export data</p>
      </td>
      <td style="text-align:left">HDX feature only available in some deployments.</td>
    </tr>
    <tr>
      <td style="text-align:left">hxl_tags</td>
      <td style="text-align:left">Stores all the available HXL tags</td>
      <td style="text-align:left">Settings -&gt; Export data</td>
      <td style="text-align:left">HDX feature only available in some deployments.</td>
    </tr>
    <tr>
      <td style="text-align:left">layers</td>
      <td style="text-align:left">Used to determine which map layers wee have available.</td>
      <td style="text-align:left">Settings -&gt; General contains the list of layers</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">media</td>
      <td style="text-align:left">Stores info about images uploaded to posts.</td>
      <td style="text-align:left">
        <p>Add post</p>
        <p>
          <br />Edit post</p>
        <p>
          <br />View post</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">messages</td>
      <td style="text-align:left">Stores SMS, Twitter and other messages and their relationship to a contact.</td>
      <td
      style="text-align:left">
        <p>Edit/Structure post</p>
        <p>
          <br />View post</p>
        <p>
          <br />Settings -&gt; Data Sources</p>
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">notification_queue</td>
      <td style="text-align:left">Keeping track of which kinds of notifications have been requested by users</td>
      <td
      style="text-align:left">Collections/Saved searches</td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">notifications</td>
      <td style="text-align:left">The actual notifications to be sent, until they are processed and actually
        sent out (in the background)</td>
      <td style="text-align:left">Collections/Saved searches</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">oauth_access_tokens</td>
      <td style="text-align:left">Links users and their client ID to an access token generated to login</td>
      <td
      style="text-align:left">-</td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">oauth_auth_codes</td>
      <td style="text-align:left">N/A</td>
      <td style="text-align:left">N/A</td>
      <td style="text-align:left">N/A</td>
    </tr>
    <tr>
      <td style="text-align:left">oauth_clients</td>
      <td style="text-align:left">All available clients. Used by the platform UI to get authorization to
        perform actions. We currently use 2 grants, the client_credentials one
        for anon requests which uses the oauth_clients data, and the password grant
        which uses oauth_clients and the user&apos;s login details to authenticate
        a user</td>
      <td style="text-align:left">-</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">oauth_personal_access_clients</td>
      <td style="text-align:left">NA</td>
      <td style="text-align:left">NA</td>
      <td style="text-align:left">N/A</td>
    </tr>
    <tr>
      <td style="text-align:left">oauth_refresh_tokens</td>
      <td style="text-align:left">Used to refresh Authorization tokens</td>
      <td style="text-align:left">-</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">permissions</td>
      <td style="text-align:left">Stores the permissions availble in the platform.</td>
      <td style="text-align:left">Settings -&gt; Roles</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">phinxlog</td>
      <td style="text-align:left">Used to store information about the migrations already executed in the
        platform.</td>
      <td style="text-align:left">-</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_comments</td>
      <td style="text-align:left">N/A</td>
      <td style="text-align:left">N/A</td>
      <td style="text-align:left">N/A</td>
    </tr>
    <tr>
      <td style="text-align:left">post_datetime</td>
      <td style="text-align:left">Stores post-values for form-attributes &quot;Date&quot; and &quot;Date&amp;Time&quot;.</td>
      <td
      style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_decimal</td>
      <td style="text-align:left">Stores post-values for form-attribute &quot;Number(decimal)&quot;.</td>
      <td
      style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_geometry</td>
      <td style="text-align:left">Stores OpenGIS formatted geometries</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_int</td>
      <td style="text-align:left">Stores post-values for form-attribute &quot;Number(integer)&quot;</td>
      <td
      style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_locks</td>
      <td style="text-align:left">Stores info about which posts are currently being edited and locked for
        other users to edit.</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_markdown</td>
      <td style="text-align:left">Stores post-values for form-attribute &quot;Markdown&quot;</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_media</td>
      <td style="text-align:left">Stores id for media-files uploaded in a post (form-attribute type &quot;Image&quot;).</td>
      <td
      style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_point</td>
      <td style="text-align:left">Stores post-values for form-attribute &quot;Location&quot;.</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_relation</td>
      <td style="text-align:left">Stores post-values for form-attribute &quot;Related posts&quot;.</td>
      <td
      style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
        </td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_text</td>
      <td style="text-align:left">Stores post-values for form-attribute &quot;Long Text&quot;.</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">post_varchar</td>
      <td style="text-align:left">Stores post-values for form-attributes &quot;Short text&quot;, &quot;Select
        (dropdowns)&quot;, &quot;Radio buttons&quot;, &quot;Checkboxes&quot; and
        &quot;Embed video&quot;</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">posts</td>
      <td style="text-align:left">Store basic info about each post.</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">posts_media</td>
      <td style="text-align:left">Holds info about the files that are uploaded to posts.</td>
      <td style="text-align:left">
        <p>Add Posts</p>
        <p>
          <br />View Posts</p>
        <p>
          <br />Edit Posts</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">posts_sets</td>
      <td style="text-align:left">Holds information about saved searches.</td>
      <td style="text-align:left">
        <p>Sort &amp; Filter -&gt; filter by Saved Search</p>
        <p>
          <br />Sort &amp; Filter -&gt; Save search</p>
        <p>
          <br />Sort &amp; Filter -&gt; Update search</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">posts_tags</td>
      <td style="text-align:left">Stores which categories is selected in which post.</td>
      <td style="text-align:left">
        <p>Add posts</p>
        <p>
          <br />View posts</p>
        <p>
          <br />Edit posts</p>
        <p>
          <br />Setting -&gt; Categories</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">roles</td>
      <td style="text-align:left">
        <p>Stores all</p>
        <p>defined roles.</p>
      </td>
      <td style="text-align:left">
        <p>Settings -&gt; Roles</p>
        <p>
          <br />Settings -&gt; Users</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">roles_permissions</td>
      <td style="text-align:left">Stores relations between roles and permissions</td>
      <td style="text-align:left">Settings -&gt; Roles</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">sets</td>
      <td style="text-align:left">Stores all saved searches available.</td>
      <td style="text-align:left">Sort &amp; Filter -&gt; Saved searches</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">tags</td>
      <td style="text-align:left">All categories created</td>
      <td style="text-align:left">
        <p>Settings -&gt; Categories</p>
        <p>
          <br />Settings -&gt;
          <br />Surveys -&gt; Create/Edit survey</p>
        <p>
          <br />Add post</p>
        <p>
          <br />View post</p>
        <p>
          <br />Edit post</p>
      </td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">targeted_survey_state</td>
      <td style="text-align:left">This is used to save the status of a targeted survey (which was the last
        survey question sent out to each user)</td>
      <td style="text-align:left">Settings -&gt; Surveys</td>
      <td style="text-align:left">Only available to deployments in some custom enterprise plans.</td>
    </tr>
    <tr>
      <td style="text-align:left">tos</td>
      <td style="text-align:left">Stores info of when and if each has signed the terms and conditions</td>
      <td
      style="text-align:left">-</td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">user_reset_tokens</td>
      <td style="text-align:left">used to store password reset tokens</td>
      <td style="text-align:left">Login -&gt; Forgot Your Password?</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">user_settings</td>
      <td style="text-align:left">User specific settings such as HDX api keys</td>
      <td style="text-align:left">-</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">users</td>
      <td style="text-align:left">Store users</td>
      <td style="text-align:left">Settings -&gt; Users</td>
      <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">webhook_job</td>
      <td style="text-align:left">Stores jobs generated by the system (used by the webhooks jobs queue).</td>
      <td
      style="text-align:left">Settings -&gt; Webhooks</td>
        <td style="text-align:left">-</td>
    </tr>
    <tr>
      <td style="text-align:left">webhooks</td>
      <td style="text-align:left">Stores information of each webhook such as url, method, etc</td>
      <td style="text-align:left">Settings -&gt; Webhooks</td>
      <td style="text-align:left">-</td>
    </tr>
  </tbody>
</table>