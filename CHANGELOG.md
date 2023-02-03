# Changelog

All notable changes to `nova-file-manager` will be documented in this file

## v0.8.2 - 2023-02-03

### What's Changed

- Add ability to configure Cropperjs by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/147

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.8.1...v0.8.2

## v0.8.1 - 2023-02-03

### What's Changed

- build(deps-dev): bump eslint from 8.21.0 to 8.33.0 by @dependabot in https://github.com/oneduo/nova-file-manager/pull/144
- build(deps-dev): bump eslint-plugin-vue from 9.3.0 to 9.9.0 by @dependabot in https://github.com/oneduo/nova-file-manager/pull/143
- build(deps-dev): bump @heroicons/vue from 2.0.8 to 2.0.14 by @dependabot in https://github.com/oneduo/nova-file-manager/pull/142
- build(deps-dev): bump vue-loader from 16.8.3 to 17.0.1 by @dependabot in https://github.com/oneduo/nova-file-manager/pull/140
- build(deps-dev): bump laravel/pint from 0.1.7 to 1.4.0 by @dependabot in https://github.com/oneduo/nova-file-manager/pull/138
- Use thumbnails for index field by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/137
- build(deps-dev): bump @headlessui/vue from 1.7.0 to 1.7.8 by @dependabot in https://github.com/oneduo/nova-file-manager/pull/139

### New Contributors

- @dependabot made their first contribution in https://github.com/oneduo/nova-file-manager/pull/144

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.8.0...v0.8.1

## v0.8.0 - 2023-01-27

### What's Changed

- [0.8.x] Moving to Typescript by @Rezrazi in https://github.com/oneduo/nova-file-manager/pull/103
- Added ability to select image from preview modal
- Added bulk deleted
- Added support for dependsOn
- Fixed an issue when the preview modal was not shown for a search asset which is not present in the current page

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.10...v0.8.0

## v0.7.10 - 2023-01-25

### What's Changed

- Improve third party package compatibility by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/127

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.9...v0.7.10

## v0.7.9 - 2023-01-25

### What's Changed

- Fix hidden files within folders by @miagg in https://github.com/oneduo/nova-file-manager/pull/129

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.8...v0.7.9

## v0.7.8 - 2023-01-23

### What's Changed

- Return string representation of Asset by @miagg in https://github.com/oneduo/nova-file-manager/pull/123

### New Contributors

- @miagg made their first contribution in https://github.com/oneduo/nova-file-manager/pull/123

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.7...v0.7.8

## v0.7.7 - 2023-01-23

### What's Changed

- Update FileManager.php by @Human018 in https://github.com/oneduo/nova-file-manager/pull/111

### New Contributors

- @Human018 made their first contribution in https://github.com/oneduo/nova-file-manager/pull/111

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.6...v0.7.7

## v0.7.6 - 2023-01-10

### What's Changed

- fix(store): input not being trimmed before request by @Rezrazi in https://github.com/oneduo/nova-file-manager/pull/120

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.5...v0.7.6

## v0.7.5 - 2022-11-25

### What's Changed

- Improve events by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/109

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.4...v0.7.5

## v0.7.4 - 2022-11-22

### What's Changed

- fix(pintura): change pintura option variable to avoid collision with Nova by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/106

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.3...v0.7.4

## v0.7.3 - 2022-11-14

### What's Changed

- fix(filesystem): fix singleDisk value in store on form field. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/99

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.2...v0.7.3

## v0.7.2 - 2022-11-01

### What's Changed

- Fix error translation by @pblaravel in https://github.com/oneduo/nova-file-manager/pull/96

### New Contributors

- @pblaravel made their first contribution in https://github.com/oneduo/nova-file-manager/pull/96

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.1...v0.7.2

## v0.7.1 - 2022-10-29

### What's Changed

- Add ability to resolve display as html. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/93
- Use php style translations. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/95

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.7.0...v0.7.1

## v0.7.0 - 2022-09-27

![feature](https://user-images.githubusercontent.com/2086576/192314603-bcb1a95a-5ee4-438d-98fb-d9901ac1194d.png)

This release comes packed with new features, improvements and fixes.

### 🍍 Pinia

The package moved to Pinia, a store and state management library set to become the new default for Vue projects. Since this project is still pretty young, we aim to ensure a long support of the libraries and tools we use. Pinia keeps the same paradigm as Vuex, and does provides many enhancements and improvements to the existing Vuex library.

### 📖 Folder upload support

You want to upload a folder and all of its content ? We got you covered ! Just drag and drop your folders and we will handle the rest.

![ezgif com-gif-maker (2)](https://user-images.githubusercontent.com/81156495/192477947-1c1a024e-7327-4ce2-bc70-f7187e4bf62f.gif)

### 🔍 Spotlight search

Where's my car dude ?

With this new release, we introduce a new spotlight-like search feature, it will allow quick access to your folders and files.

![spotlight-dark](https://user-images.githubusercontent.com/81156495/192477149-c24a3618-a34b-4005-a364-da89ce633d28.png)

### 🧭 Onboarding tour

Want to give your users a guided tour of the file manager ? We got you !
You can now enable a quick and short tour of the tool to guide your users.

### 💼 Unzip archives

What is that ? A zip archive ? Arrrrghh

We used to say that before, but zip files do not scare us anymore, you can unzip and put your content outside the archive right from the file manager. Easy, quick, simple.

### ✨ PDF Viewer

Do you even PDF ?

Just slap a PDF embed viewer in the tool, this way it is more convenient and could spare you linking the wrong files to the wrong person.

### 💄 Pintura Image Editor

![pintura-light](https://user-images.githubusercontent.com/81156495/192477190-f558f6a7-9756-4ab4-b362-78d120761a21.png)

Looking for a fully featured image editor ? We got you covered. We are pleased to announce we have added a new integration for Pintura, an image editor by [Rik Schennink](https://github.com/rikschennink)

Please note that Pintura is a paid library and is not included in Nova File Manager.

You need to acquire a license and then follow the steps in the documentation to set up your integration.

Learn more about [Pintura image editor by PQINA](https://pqina.nl/pintura/?affiliate_id=775099219). (Affiliate link)

### 🚨 BREAKING CHANGE - VENDOR NAME CHANGE

With this new release, we have moved to a new vendor, we have updated our repo and the packagist registry for our package. This change should not introduce any unwanted behavior, however, if you encounter any issues you may need to run **one (or all) of the following commands** :

```bash
rm -rf vendor/














```
```bash
composer install oneduo/nova-file-manager














```
```bash
composer rm bbs-lab/nova-file-manager














```
> **Note**
In case you're experiencing trouble setting up the package with the new vendor, please open a new issue and we will look into it.

### What's Changed

- [0.7.x] Improving components, store and backend handlers by @crezra in https://github.com/oneduo/nova-file-manager/pull/82
- Add the Authenticate middleware, without which the side menu does not appear by @vesper8 in https://github.com/oneduo/nova-file-manager/pull/84
- Add Pintura image editor support by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/85

### New Contributors

- @vesper8 made their first contribution in https://github.com/oneduo/nova-file-manager/pull/84

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.6.4...v0.7.0

## v0.6.4 - 2022-09-10

### What's Changed

- fix(upload): fix an issue where a file could not be uploaded in a subdirectory when a file with same name exists in the root folder by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/79

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.6.3...v0.6.4

## v0.6.3 - 2022-09-10

### What's Changed

- refactor: add optional `resource` route parameter on api routes to comply with NovaRequest resource resolving by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/78

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.6.2...v0.6.3

## v0.6.2 - 2022-09-09

### What's Changed

- docs: updated docs with update checker config by @crezra in https://github.com/oneduo/nova-file-manager/pull/74
- docs: added upload validation documentation by @crezra in https://github.com/oneduo/nova-file-manager/pull/75
- fix(flexible): fix flexible support by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/77

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.6.1...v0.6.2

## 0.6.1 - 2022-09-09

### What's Changed

- fix: added missing update checker banner by @crezra in https://github.com/oneduo/nova-file-manager/pull/73

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.6.0...v0.6.1

## 0.6.0 - 2022-09-09

### What's Changed

- Add workflow to automatically update assets via `nova-kit/nova-packages-tool` by @crynobone in https://github.com/oneduo/nova-file-manager/pull/64
- Bump supported version with Script ordering fixes. by @crynobone in https://github.com/oneduo/nova-file-manager/pull/65
- test: add guzzle to fix browser test. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/66
- Minor fixed and UI improvements by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/67
- refactor: refactor index field by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/68
- Support filesystem as string by @milewski in https://github.com/oneduo/nova-file-manager/pull/69
- feat(upload): add upload rules and upload custom validation by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/71
- feat(flexible): add flexible content support by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/72
- feat: add update checker banner message by @crezra

### New Contributors

- @crynobone made their first contribution in https://github.com/oneduo/nova-file-manager/pull/64

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.5.0...v0.6.0

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

- fix(docs): updated screenshots in docs by @crezra in https://github.com/oneduo/nova-file-manager/pull/59
- Add crop image feature by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/60
- [0.5.x] Improved multi disk support, added image cropping by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/63

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.4.0...v0.5.0

## 0.4.0 - 2022-09-05

### What's Changed

- Add field and tool permissions. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/49
- fix(file-card): fixed missing file label spacing and alignment by @crezra in https://github.com/oneduo/nova-file-manager/pull/56
- fix(file-card): fixed file card submitting the form by @crezra in https://github.com/oneduo/nova-file-manager/pull/57
- fix(disks): fixed path not resetting when changing disks by @crezra in https://github.com/oneduo/nova-file-manager/pull/58

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.3.2...v0.4.0

## 0.3.2 - 2022-09-02

### What's Changed

- sync by @crezra in https://github.com/oneduo/nova-file-manager/pull/47
- ♿️ fixed focus styles for better accessibility  by @crezra in https://github.com/oneduo/nova-file-manager/pull/48

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.3.1...v0.3.2

## 0.3.1 - 2022-09-02

### What's Changed

- Improve selection and accessibility by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/46

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.3.0...v0.3.1

## 0.3.0 - 2022-09-02

### What's Changed

- v0.3.0 by @crezra in https://github.com/oneduo/nova-file-manager/pull/45

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.7...v0.3.0

## 0.2.7 - 2022-08-31

### What's Changed

- 💄 fixed some minor UI issues by @crezra in https://github.com/oneduo/nova-file-manager/pull/43

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.6...v0.2.7

## 0.2.6 - 2022-08-31

## What's Changed

- 🔧 added algolia docsearch by @crezra in https://github.com/oneduo/nova-file-manager/pull/35
- 📈 added tinyanalytics to docs by @crezra in https://github.com/oneduo/nova-file-manager/pull/36
- 📈 fixed tinyanalytics setup by @crezra in https://github.com/oneduo/nova-file-manager/pull/37
- 📈 replace tinyanalytics with splitbee by @crezra in https://github.com/oneduo/nova-file-manager/pull/38

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.5...v0.2.6

## 0.2.5 - 2022-08-14

## What's Changed

- 📝 updated docs by @crezra in https://github.com/oneduo/nova-file-manager/pull/25
- 👷 Add test workflow. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/26
- 👷 Update test workflow. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/27
- 👷 Update test workflow. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/28
- Develop by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/29
- Develop by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/30
- 👷 Update test workflow. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/31
- Update documentation by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/32
- 🐛 fixed file selection in the form field by @crezra in https://github.com/oneduo/nova-file-manager/pull/34

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.4...v0.2.5

## 0.2.4 - 2022-08-13

## What's Changed

- 📝 Update documentation. by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/18
- Linted JS and added documentation by @crezra in https://github.com/oneduo/nova-file-manager/pull/19
- 💚 updated github workflow by @crezra in https://github.com/oneduo/nova-file-manager/pull/20
- updated vitepress config by @crezra in https://github.com/oneduo/nova-file-manager/pull/21
- 🔧 updated vitepress config with base url by @crezra in https://github.com/oneduo/nova-file-manager/pull/22
- 🔧 changed simultaneousUploads on chunk uploads by @crezra in https://github.com/oneduo/nova-file-manager/pull/23
- 🌐 updated localization keys by @crezra in https://github.com/oneduo/nova-file-manager/pull/24

## New Contributors

- @crezra made their first contribution in https://github.com/oneduo/nova-file-manager/pull/19

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.3...v0.2.4

## 0.2.3 - 2022-08-13

## What's Changed

- Fix str support and scroll issue by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/17
- Fixed an issue with str on Laravel 8
- Fixed an issue where overflow is set to hidden after renaming a file
- Fixed an issue where the oldPath parameter was wrong
- Fixed an issue with the `useError` composable

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.2...v0.2.3

## 0.2.2 - 2022-08-12

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.1...v0.2.2

- Fixed an issue with list view
- Added lint
- Added str helper for Laravel 8
- Refactor vue components to composition API

## 0.2.1 - 2022-08-11

## What's Changed

- Fix incompatibility with `nova-flexible-content` by @milewski in https://github.com/oneduo/nova-file-manager/pull/15

## New Contributors

- @milewski made their first contribution in https://github.com/oneduo/nova-file-manager/pull/15

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.2.0...v0.2.1

## 0.2.0 - 2022-08-10

## What's Changed

- Develop by @mikaelpopowicz in https://github.com/oneduo/nova-file-manager/pull/14

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.1.0...v0.2.0

Closes https://github.com/oneduo/nova-file-manager/issues/4

Closes partially https://github.com/oneduo/nova-file-manager/issues/5

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

**Full Changelog**: https://github.com/oneduo/nova-file-manager/compare/v0.0.1-alpha...v0.0.1

## v0.0.1-alpha - 2022-06-20

- 📝 updated docs
