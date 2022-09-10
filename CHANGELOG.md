# Changelog

All notable changes to `nova-file-manager` will be documented in this file

## v0.6.4 - 2022-09-10

### What's Changed

- fix(upload): fix an issue where a file could not be uploaded in a subdirectory when a file with same name exists in the root directory by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/79

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.6.3...v0.6.4

## v0.6.3 - 2022-09-10

### What's Changed

- refactor: add optional `resource` route parameter on api routes to comply with NovaRequest resource resolving by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/78

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.6.2...v0.6.3

## v0.6.2 - 2022-09-09

### What's Changed

- docs: updated docs with update checker config by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/74
- docs: added upload validation documentation by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/75
- fix(flexible): fix flexible support by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/77

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.6.1...v0.6.2

## 0.6.1 - 2022-09-09

### What's Changed

- fix: added missing update checker banner by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/73

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.6.0...v0.6.1

## 0.6.0 - 2022-09-09

### What's Changed

- Add workflow to automatically update assets via `nova-kit/nova-packages-tool` by @crynobone in https://github.com/BBS-Lab/nova-file-manager/pull/64
- Bump supported version with Script ordering fixes. by @crynobone in https://github.com/BBS-Lab/nova-file-manager/pull/65
- test: add guzzle to fix browser test. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/66
- Minor fixed and UI improvements by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/67
- refactor: refactor index field by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/68
- Support filesystem as string by @milewski in https://github.com/BBS-Lab/nova-file-manager/pull/69
- feat(upload): add upload rules and upload custom validation by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/71
- feat(flexible): add flexible content support by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/72
- feat: add update checker banner message by @crezra

### New Contributors

- @crynobone made their first contribution in https://github.com/BBS-Lab/nova-file-manager/pull/64

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.5.0...v0.6.0

## 0.5.0 - 2022-09-08

### 🚨 BREAKING CHANGE

> **Warning**

Existing assets saved with the field will not work anymore once you update the tool, please migrate your existing data when updating the package.

This PR introduces a major breaking changing related to how the data is saved in the database, initially we tried to follow Laravel Nova's built-in fields as much as possible by having a two column set up to save the asset's path and then the asset's disk if needed. However, this approach isn't as reliable as we hoped for it to be. There was indeed a particular use case that wasn't in our scope when building the field, when saving multiple assets from different disks, only the one disk was to be saved in the disk column and that makes the tool behaves in unintended ways.

We have moved forward with a JSON schema to save the field's data.

Starting from the next 0.5 release, the data will change from :

| image                | image_disk    |
|----------------------|---------------|
| `<image-path-value>` | `<disk-name>` |

TO

| image                                                                |
|----------------------------------------------------------------------|
| `{"path": "<image-path-value>",   "disk": "<disk-name>" } ` |

So in other words, each asset that will saved with its corresponding disk.

We regret to introduce such a change so early, but it is a change that needs to be done unfortunately.

To migrate your existing data, you may defined a command or action to transform your data to the required schema.

#### What's Changed

- fix(docs): updated screenshots in docs by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/59
- Add crop image feature by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/60
- [0.5.x] Improved multi disk support, added image cropping by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/63

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.4.0...v0.5.0

## 0.4.0 - 2022-09-05

### What's Changed

- Add field and tool permissions. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/49
- fix(file-card): fixed missing file label spacing and alignment by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/56
- fix(file-card): fixed file card submitting the form by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/57
- fix(disks): fixed path not resetting when changing disks by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/58

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.3.2...v0.4.0

## 0.3.2 - 2022-09-02

### What's Changed

- sync by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/47
- ♿️ fixed focus styles for better accessibility  by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/48

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.3.1...v0.3.2

## 0.3.1 - 2022-09-02

### What's Changed

- Improve selection and accessibility by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/46

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.3.0...v0.3.1

## 0.3.0 - 2022-09-02

### What's Changed

- v0.3.0 by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/45

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.7...v0.3.0

## 0.2.7 - 2022-08-31

### What's Changed

- 💄 fixed some minor UI issues by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/43

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.6...v0.2.7

## 0.2.6 - 2022-08-31

## What's Changed

- 🔧 added algolia docsearch by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/35
- 📈 added tinyanalytics to docs by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/36
- 📈 fixed tinyanalytics setup by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/37
- 📈 replace tinyanalytics with splitbee by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/38

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.5...v0.2.6

## 0.2.5 - 2022-08-14

## What's Changed

- 📝 updated docs by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/25
- 👷 Add test workflow. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/26
- 👷 Update test workflow. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/27
- 👷 Update test workflow. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/28
- Develop by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/29
- Develop by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/30
- 👷 Update test workflow. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/31
- Update documentation by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/32
- 🐛 fixed file selection in the form field by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/34

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.4...v0.2.5

## 0.2.4 - 2022-08-13

## What's Changed

- 📝 Update documentation. by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/18
- Linted JS and added documentation by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/19
- 💚 updated github workflow by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/20
- updated vitepress config by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/21
- 🔧 updated vitepress config with base url by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/22
- 🔧 changed simultaneousUploads on chunk uploads by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/23
- 🌐 updated localization keys by @crezra in https://github.com/BBS-Lab/nova-file-manager/pull/24

## New Contributors

- @crezra made their first contribution in https://github.com/BBS-Lab/nova-file-manager/pull/19

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.3...v0.2.4

## 0.2.3 - 2022-08-13

## What's Changed

- Fix str support and scroll issue by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/17
- Fixed an issue with str on Laravel 8
- Fixed an issue where overflow is set to hidden after renaming a file
- Fixed an issue where the oldPath parameter was wrong
- Fixed an issue with the `useError` composable

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.2...v0.2.3

## 0.2.2 - 2022-08-12

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.1...v0.2.2

- Fixed an issue with list view
- Added lint
- Added str helper for Laravel 8
- Refactor vue components to composition API

## 0.2.1 - 2022-08-11

## What's Changed

- Fix incompatibility with `nova-flexible-content` by @milewski in https://github.com/BBS-Lab/nova-file-manager/pull/15

## New Contributors

- @milewski made their first contribution in https://github.com/BBS-Lab/nova-file-manager/pull/15

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.2.0...v0.2.1

## 0.2.0 - 2022-08-10

## What's Changed

- Develop by @mikaelpopowicz in https://github.com/BBS-Lab/nova-file-manager/pull/14

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.1.0...v0.2.0

Closes https://github.com/BBS-Lab/nova-file-manager/issues/4

Closes partially https://github.com/BBS-Lab/nova-file-manager/issues/5

- Fix multiple fields on same resource
- Fix scroll issue when closing modals
- Add limit option on FileManager field
- Improve store to handle multiple fields
- Improve UI
- Override resolveAttribute instead of resolve (#12)

## 0.1.0 - 2022-08-05

## ✨ New features

- added search to the browser tool
- added multiple selection on resource fields
- added a new preview modal for files
- improved UI

## 0.0.1 - 2022-08-05

**Full Changelog**: https://github.com/BBS-Lab/nova-file-manager/compare/v0.0.1-alpha...v0.0.1

## v0.0.1-alpha - 2022-06-20

- 📝 updated docs
