# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
### Fixed
### Added

## [101.1.0] - 2022-10-22
### Added
- attribute event code
- multiple attibute backend models
- default value providers
### Changed
- max length validator modifications

## [101.0.17] - 2022-10-12
### Changed
- attribute_code field length in DB to 255 chars
### Added
- validator codes
- new attribute sources: emailIdentity, emailTemplate
### Fixed
- validators for empty value
- textarea valdator returned value

## [101.0.16] - 2022-10-06
### Added
- fix for saving default values of new options

## [101.0.15] - 2022-10-05
### Added
- default values for select and multiselect attributes

## [101.0.14] - 2022-10-04
### Added
- input validator option for input types
- backend validation for textarea input length

## [101.0.13] - 2022-09-28
### Fixed
- fix backward compatibility option label per store

## [101.0.12] - 2022-09-28
### Fixed
- fix option label per store (translations)

## [101.0.11] - 2022-09-27
### Added
- input params (max length for textarea)
- multseelct can be visible on grid
- more protection for images upload

## [101.0.10] - 2022-09-22
### Fixed
- fix for multiselect options in form

## [101.0.9] - 2022-08-08
### Fixed
- check errors in $_FILES on uploaded files

## [101.0.8] - 2022-07-07
### Fixed
- fixed filter on collection for global attributes when store Id is set

## [101.0.7] - 2022-04-04
### Fixed
- fix for addFilterToMap on entity collection
### Added
- saveAttributeValue method on entity 

## [101.0.6] - 2021-11-16
### Added
- categories options source

## [101.0.5] - 2021-10-27
### Fixed
- fix for required value condition
### Added
- disable/enable attribute options source
- attribute extra params
- default value for boolean (for widget forms module)

## [101.0.4] - 2020-09-18
### Added
- Defined protected attribute in abstract Entity class

## [101.0.3] - 2020-09-18
### Added
- Added compatibility with Magento 2.4.0

## [101.0.2] - 2020-09-18
### Added
- Added compatibility with PHP 7.4

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
