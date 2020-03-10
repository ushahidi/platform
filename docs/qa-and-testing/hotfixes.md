---
description: >-
  A hotfix is a fix to an issue that has been discovered on production that
  needs to be fixed and go live ASAP.
---

# Hotfixes

Usually a hotfix takes priority and it will be deployed to the test environment. Testing and verification of the hotfix is guided by a checklist that could reside either on the issue or the corresponding pull request. Once fix has been verified, smoke tests are then run guided by the [smoke testing suite for platform](https://ushahidi.ontestpad.com/script/30#//). Once these tests pass, then the hotfix is merged into production.

