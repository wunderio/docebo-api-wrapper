# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [1.2.0] - 2016-04-22
### Added
- Can now perform raw call to check username which will return the body content from the API. This is useful if you wish to access the information returned in the response, i.e. `firstname`, `lastname`, `email`.
- Updated `usernameExists` and `emailExists` to make use of the raw method rather than build the query within themselves.

## [1.1.3] - 2016-04-22
### Fixed
- Updated readme.md to show the correct way to load library via composer as it isn't yet registered with packagist.

## [1.1.2] - 2016-04-21
### Fixed
- Changed required version of Carbon to match dev-master for that project.

## [1.1.1] - 2016-04-21
### Fixed
- `usernameExists` and `emailExists` methods now return `boolean` response as they're being asked a true/false question.

## [1.1.0] - 2016-04-21
### Added
- Can now lookup whether a Docebo account exists based on a username or email address.

## [1.0.0] - 2016-04-21
- Initial release.