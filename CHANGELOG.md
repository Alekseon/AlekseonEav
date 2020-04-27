# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
### Fixed
### Added

## [101.0.0] - 2020-04-27
### Changed
- changed variables in entity resource from private to protected
- return frontendInputTypes as data objects
- removed storeManager from entity resource constructor
- changed context model in entity constructor
- added getStore method to entity and get default store id from store manager
- made attribute code editable if attribute was created by user
### Added
- attributes group codes
- added method getFrontendInputTypeConfig on atribute model
- validations (email and number)
### Fixed
- fix for "has_option_codes" column name in schama installator

## [100.2.1] - 2019-08-29
### Fixed
- Fix for Option Codes in abstract source model

## [100.2.0] - 2019-06-10
### Added
- Option Codes

## [100.1.3] - 2019-06-04
### Added
- check if option with ID already exists in options installator
- require prototype for all elements in entity edit form

## [100.1.2] - 2019-04-18
### Added
- optional parameter paramName for extractValueFromRequest method

## [100.1.1] - 2019-04-18
### Added
- Attribute metadata forms
- metadata form class for date type attribute

## [100.0.12] - 2018-11-22
### Fixed
- fix for method AttributeRepository::getAttributeBuCode

## [100.0.10] - 2018-11-20
### Fixed
- disable element on entity edit page if its set to use default value [refs #7]
- fixed issue with saving images [refs #35] 

## [100.0.9] - 2018-10-17
### Changed
- Input Types are more dynamic, and defined in di.xml [refs #30]
### Fixed
- fix for hidding input elements on attribute edit page when input type value changes [refs #29]
- small improvement for getAttribute() method
- fixed sorting values on entity grid [refx #33]

## [100.0.8] - 2018-10-10
### Fixed
- small fix for previous commit

## [100.0.7] - 2018-10-10
### Fixed
- fix for entity attributes, it was posible that getAllLoadedAttributes returns false as attributes

## [100.0.6] - 2018-10-03
### Fixed
- fix for grid constructor

## [100.0.5] - 2018-10-02
### Added
- wysiwyg for textarea attributes.
- form attributes renderers
- sort order column on attributes list
- attribute frontend labels
- renderer for image attributres for grid
### Fixed
- eav grid prepare collection method
- some small issues

## [100.0.4] - 2018-04-15
