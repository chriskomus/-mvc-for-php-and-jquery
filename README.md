# MVC for PHP, Bootstrap, and jQuery

I created this as a way to start new PHP projects quickly by building a template for a CMS with PHP, Bootstrap, and jQuery in an MVC framework using almost no external packages.

## Overview

A straightforward CMS for PHP, Bootstrap/Bootswatch, and jQuery in an MVC (Model-View-Controller) framework using almost no additional packages. The models and controllers are handled by PHP, while the view is handled both by PHP (for displaying the page template) and Javascript/jQuery (for displaying the data on the page).

Lightweight and fast. Comes with basic user authentication, database connectivity, API, datagrids, tiles, and item details page. This template is built with the following models: products, product categories, and users. However, it's easy to add new models, or change up the default model. ie: Blog posts, image gallery, album discography, etc.

## Features:

### Displaying Items from a Model
- **Catalog view**. View tiles with item summary, click for modal popup view and detailed page view for a page with all model details.
- **Datagrid view**. For logged-in users, models/items are displayed in a datagrid with link to edit a model.
- **Categories** - Models/items can be categorized. Display items from individual categories in both the catalog view and datagrid view. 
- **Datagrid with pagination, sorting, filtering and search.** I have also made the datagrid available as its own repo.
- **Archive Items** - Archive items to hide from the catalog/logged-out user pages.
- **URL Slugs for categories and items** - Generate SEO-safe URLs for model items and categories.
- **Image Uploading** - Associate an image with an item.
- **API** for retrieving data and displaying it on the page with Javascript.
- **Data Validation** Data is validated both on the front end (using JavaScript and Bootstrap's data validation features), and on the back end (in the model's methods). See the Product model and view for examples on how this can be implemented.

### Built-In Authentication and RBAC
- **Basic Authentication**. Login/logout/register users. Users can edit account details and add address info. Admins can edit any user, and change user roles. 
- **Role based authentication control (RBAC)**. User types (admin, user, guest). Control access to pages and menu items based on a user's role/user type. 
- **Restrict guest access**. Allow unauthenticated users to view a front page catalog of items or restrict access to only show a login page.
- **Restrict guest registrations**. Allow or forbid unauthenticated users to create an account.
- **Dynamic RBAC menu.** Only show the menu items available to authorized users with the correct roles/user types.
- **Addresses for users.** With some limited address validation.

### Additional Features
- **Dark Mode.** Toggle between light mode and dark mode.
- **Error and message handling.** Pass errors and messages from the back end to the front end.
- **Cookies** - Store user cookies for dark mode, datagrid sort defaults, etc.

## Get Started

1. Clone this repo.
2. Import the default sql file. It contains the required settings table:
`mvc-for-php-and-jquery.sql`
3. Run `composer install`
4. Get a Tinymce and Font Awesome account, and update the references in app/views/includes/head.php
5. Update the settings table with your desired information
6. In the Settings table set guests_can_register to True. This allows guests to create new accounts.
7. Open \public\.htaccess and modify the following line to point to the relative path on the server. IE: If site is located at www.example.com/mvc-for-php-and-jquery/ then RewriteBase should be: /mvc-for-php-and-jquery/public
   `RewriteBase /coding/mvc-for-php-and-jquery/public`
8. Open app/config/config.php and edit database settings.
9. Start up the PHP server and check that the page loads correctly.
10. Create an account by clicking Register in the top menu. This will be the new Admin account. After creating the account you will need to manually set it to Admin user type. Go into the Users table, change the user type to 'a'. From now on this can be managed in the Users page, rather than editing the database. However, for obvious security reasons, a user can't set their own user type when creating an account, and no other admin accounts would be available at installation time.
11. Try adding sample users, products, or categories. Everything should be working now.

## MVC Naming Conventions and Creating New Models

Most of this should be straightforward as it follows naming conventions.

Various functionality throughout the site relies on taking controllers and getting the singular model name, and vice versa, and interacting with the database and Javascript with an expectation of matching names. Therefore, not sticking to the proper naming convention will break the site.

**Each model requires the following associated entities and files:**
- Model: app/models/Product.php (SingularCamelCase)
- Controller: app/controllers/Products.php (PluralCamelCase)
- PHP View: app/views/products/index.php and app/views/products/product.php (plural-kebab-case/singular-kebab-case.php)
- JavaScript View: public/javascript/controllers/products.js (plural-kebab-case)
- Images Directory *[optional]*: public/images/products/ (plural-kebab-case)
- SQL Table: products (plural_snake_case)
- SQL View: products_view (plural_snake_case + _view)
- SQL Column: quantity (singular_snake_case)

Each controller's associated views will be in a subdirectory of the app/views directory with the same name as the controller in kebab-case (ie: /products/index.php). The list view of all records should be /controller-name/index.php and individual records should be /controller-name/model.php. Some exceptions apply (ie: views of the Users controller).

The filenames within each app/views subdirectory are not as strict and can be named freely, however a controller must have a matching method for each file (ie: the Product controller must have a method named index for the view: /products/index.php). Methods of a controller don't always need their own view, as long as they redirected somewhere. View directory names are strict, and must match a controller.

Each controller has an associated JavaScript file that is in the public/javascript directory that must match the name of the controller. This JavaScript file is responsible for displaying data returned from the API.

Each table in the database can also have an associated view with additional data from other tables. The view is used to validate all columns available when displaying the list of records, and also sometimes used in the models for selecting records with additional data, to avoid repetitive JOIN clauses.

## Methods in Model Classes

For easy readability the methods in each model are in the following order and carry out the following operations.
Note that not all models will have all of these methods. They are in camelCase.

- create - INSERT a new record
- update - UPDATE a record
- update[FieldName] - UPDATE the specified column of a record
- delete - DELETE a record WHERE there is a matching primary key id
- deleteAll - DELETE all records in the associated database table
- archive - Archive a record
- clear[FieldName] - UPDATE a specific column in the associated database table to null
- clearAll[FieldName] - UPDATE and set an entire specified column to null in the associated database table
- get - SELECT a single record WHERE there is a matching primary key id
- getAll - SELECT all records in the associated database table
- getBy[FieldName] - SELECT a single record by a specified column
- get[FieldName] - SELECT a single field of a record by a matching primary key id
- get[FieldName]By[FieldName] - SELECT a single field of a record by a specified column
- getQuery - A SELECT query used by multiple methods
- validate - Validate the provided data that will be used to create or update a record
- validateAll - Validate all records in the associated database table
- validate[FieldName] - Validate the provided data for a specified column
- bindAll - Bind all fields that will be used to create or update a record
- is[FieldName]Exist - A bool that determines whether a record exists based on a specified field name
- is[FieldName]Set - A bool that determines whether a field in a record is not null
- count - Get a count of the number of records returned
- [fieldname]Count - Get a count of the number of records returned based on a specified field name
- [model_specific_functionality] - Various methods that are specific to a model

## Methods in Controller Classes

For easy readability the methods in each controller are in the following order and carry out the following operations.
Note that not all models will have all of these methods. They are in snake_case.

- index - Required: Generally this will display a list of records, or provide data for a default view
- [controller_name] - When index does not provide a list of records, use this method to provide a list instead
- [controller_name_singular/model] - Display a single record
- create - Should only be accessible by POST: Provide functionality to create a new record
- update - Should only be accessible by POST: Provide functionality to update a record
- delete - Should only be accessible by POST: Provide functionality to delete a record
- delete_[field_name] - Should only be accessible by POST: Provide functionality to clear a field in a specific record and delete an assoicated file
- archive - Should only be accessible by POST: Provide functionality to archive a record
- upload_[field_name] - Should only be accessible by POST: Provide functionality to update a field in a record and upload an associated file
- export - Provide functionality to export a file of records
- generate_[controller_name_singular/model]_slugs - Generate new slugs for all records of a model
- validate_[field_name] - Validate a field name for a provided record
- validate_[field_name_plural] - Validate field names for all records
- add_remove_columns - Add or remove a column from the list of records view
- reset_columns - Reset list of shown columns to the settings default
- [controller_specific_functionality] - Various methods that are specific to a controller

## Functions in .js Views

While the PHP view creates the template, the JavaScript view generates DOM elements, displays the page data/content, and handles JavaScript events such as clicking buttons and modal popups. 

- load - Set events for various DOM elements on page load, such as buttons and modals.
- loadSiteWithExtraData - Controller specific API data. This is called from main.js after Promise.all has been fulfilled.
- generateItems - Generate DOM elements on the page from a data source.
- deleteModal - This function generates a modal popup for deleting an item.
- archiveModal - This function generates a modal popup for archiving an item.
- uploadImageModal - This function generates a modal popup for uploading an image to associate with an item.
- deleteImageModal - This function generates a modal popup for deleted an image associated with an item.
- modalHide - This function hides a modal when it is closed. 

## API Controller

The entire API is one Controller. It has a plural method name and a singular method name for each controller, ie: products and product. The plural name will display all results, the singular name should be accompanied by a field (usually the id) to display a single result.

## Limitations and Security

- User access relies on password_hash() and password_verify(), which is secure enough for the scope of this repo.
- Cookies are not encrypted. Therefore, since RBAC uses unencrypted cookies to determine a user's type, a user could fraudulently change their RBAC cookie to give themselves Admin access. This could be protected using JWT or any number of other options, but is beyond the scope of this repo.

## Packages and Requirements

Run `composer install` to add required packages for image resizing and validation.

Additional 3rd party requirements:
- Tinymce
- Font Awesome
- Google Fonts
- Bootstrap
- jQuery

## Screenshots

![Catalog Tile View](https://github.com/chriskomus/mvc-for-php-and-jquery/blob/main/public/images/sample/tiles.jpg?raw=true)

![Datagrid](https://github.com/chriskomus/mvc-for-php-and-jquery/blob/main/public/images/sample/datagrid.jpg?raw=true)

![Users](https://github.com/chriskomus/mvc-for-php-and-jquery/blob/main/public/images/sample/users.jpg?raw=true)

![Detailed Item View](https://github.com/chriskomus/mvc-for-php-and-jquery/blob/main/public/images/sample/detailed-view.jpg?raw=true)

![Login Page](https://github.com/chriskomus/mvc-for-php-and-jquery/blob/main/public/images/sample/login.jpg?raw=true)