CRUDlex Changelog
=================

## 0.9.7
Released: Upcoming
- CRUDData::listEntries() can take now operators for the filter parameter
- Added filters for the list views
- Big cleanup of the CRUDEntityDefinition constructor
- Not required number fields not being entered by the user end up the database as NULL instead of as 0
- null is a valid value when validating an int, float or reference which is not required in an entity
- Added classes to the create, edit and delete forms so they can be hooked easily with JavaScript

## 0.9.6
Released: 2015-02-20
- Added the possibility to cascade delete children
- Added ids to the rendered fields of an entity details page so they can be hooked easily with JavaScript
- Added ids and classes to the rows and columns of the entity list page so they can be hooked easily with JavaScript
- Added ids and classes all buttons so they can be hooked easily with JavaScript
- Added an entity related id to the page wrapping div so everything can be hooked easily with JavaScript
- Fixed a SQL error for not required date and datetime fields where no value was selected
- Fixed a SQL error for not required reference fields where no value was selected
- Updated dependencies to:
    - Bootstrap 3.3.1
    - moment.js 2.8.4
    - "symfony/twig-bridge": "~2.6"
    - "doctrine/dbal": "~2.5"
    - "symfony/yaml": "~2.6"
    - "phpunit/phpunit": "~4.5",
    - "satooshi/php-coveralls": "0.7.*@dev",
    - "symfony/browser-kit": "~2.6",
    - "symfony/css-selector": "~2.6",
    - "apigen/apigen": "4.0.0"
- Added pagination to the list view
- Added the ability to hand in default values to the create form
- Added edit and delete buttons to the children table of the entities show page
- CRUDMySQLData::listEntries handles null filter values now correctly
- Fixed values are also returned in CRUDEntity::get() now
- Huge performance optimization when listing entities referencing other entities

## 0.9.5
Released: 2014-11-07
- Changed CRUDEntityDefinition::addParent() to CRUDEntityDefinition::addChild() and CRUDEntityDefinition::getParents() to CRUDEntityDefinition::getChildren() as it was confusing
- Added the option to show the referencing children on the parents details page
- Nicer requirements in the composer.json, less strict (~1.2 instead of 1.2.1 for example)
- Modifications of the entity definition (changing the type) doesn't require the field to exist anymore; this prepares the runtime modification of the whole definition
- API docs are written now
- Updated dependencies to: Bootstrap 3.3.0, moment.js 2.8.3
- Removed all usages of CDNs for CSS and JS resources and delivering them from the filesystem
- Added support for float fields.

## 0.9.4
Released: 2014-10-07
- The input placeholders now use the field label as well and don't use just the fieldlabel anymore
- url fields in the list view get cut after 27 characters now with "..." at the end, the full url is still available as tooltip
- A first version of file uploads
- Introducing the addons repository with Amazon S3 file support
- Added the possibility to extend the CRUDControllerProvider which had a hard coded reference to itself before
- Added the type "fixed" which always has a fixed value
- A type of a field can be changed in runtime now via a setter of the CRUDEntityDefinition
- A required constraint of a field can be changed in runtime now via a setter of the CRUDEntityDefinition

## 0.9.3
Released: 2014-09-09
- Rearranged the button positions, sizes and colors
- Multiline fields get cut after 27 characters now with "..." at the end, the full text is still available as tooltip
- URL fields show only their base name in the list view, but are still clickable to the full URL
- Custom layouts for the sections create, list, show and edit
- Custom layouts for the sections create, list, show and edit of specific entities
- Custom layouts for specific entities
- Supporting bool fields
- CRUDEntitity::get($fieldName) casts the fields now to int and booleans if the type is int or bool
- Added a manual

## 0.9.2
Released: 2014-09-04
- Creating an entity with the DB-layer doesn't return the id anymore but updates the entity field "id"
- Correctly displaying the name of the referenced entities in the list and details page
- 100% test coverage!
- 404 if trying to delete a non existant entity
- Added the possibility to define the fields shown in the list view
- Added a getter for the definition of an entity
- Supporting multiline text fields
- New datetime picker, used for dates and datetimes
- Supporting datetime fields
- Supporting set fields
- Supporting URL fields

## 0.9.1
Released: 2014-08-31
- Fixed an exception in PHP 5.3.3 coming from the visibility of an internal function being called from a closure
- Added the missing requirement "symfony/yaml" to the composer.json
- Reorganized source and resources like templates
- The entities can have labels now
- The fields can have labels now
- Added first PHPUnit tests

## 0.9
Released: 2014-08-29

First release.
