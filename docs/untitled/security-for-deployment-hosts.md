# Security for deployment hosts

The purpose of this guide is not so much providing complete cyber-security training, but highlighting some of the system setup aspects that are most important and/or specific to Platform security.

## Essential check-list

* [ ] The server is hosted in a provider and geography/legislation that doesn't put the mission of the deployment at risk.
* [ ] HTTPS protocol \(TLS\) is enabled and securely configured **for both API and client endpoints**.
* [ ] There is some sort of effective log rotation mechanism, preferably together with a low-level wiping mechanism.
  * [ ] On the Ushahidi Platform API installation folder under `storage/logs`
  * [ ] For the web server, PHP and MySQL logs as well
* [ ] If hosting in a cloud or VPS provider, disk encryption with a specific ephemeral key is used.
* [ ] Backups are scheduled, monitored, encrypted and regularly tested.
* [ ] Latest updates are installed regularly for:
  * Operating system and core libraries
  * PHP, Web server and MySQL services
  * Ushahidi Platform

