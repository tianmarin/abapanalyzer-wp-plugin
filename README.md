# SAP Netweaver ABAP Analyzer Wordpress Plugin
**ABAP**Analyzer is a [WordPress](https://wordpress.org/) plugin, that checks 'SAP Netweaver ABAP AS information files' and present useful information for its administration.

## Installation
1. Just [download the ZIP file](/archive/master.zip) of this repository and save it to your computer.
2. Login to your WordPress site.
3. Go to *Plugins > Add New* and click the **Upload Plugin** button beside the heading.
4. Click the *Choose File* button and open the plugin file.
5. Click *Install Now* and wait for a message to display, letting you know the plugin installed successfully.

## Basic Activation
Once installed (and activated) the plugin will create a new **Page Template**.
Keep in mind that Page Templates *only apply* to pages, not to any other content type (like posts and custom post types).
To turn a page into a *ABAP Analyzer Page*:

1. Create (or edit) a new Wordpress Page (*Page > Add New*)
2. Change the *Page Template* (in the *Page Attributes* section) to **ABAP Analyzer App**

## Usage
In order to use **ABAP**Analyzer the SAP NW ABAP Sistem must be configured to collect information files. Details of this setup are explained step-to-step in the *intro* of the  *ABAP Analyzer Page*.
Current collectors ready to use are:

1. Snapshot Monitoring (/sdf/mon).
2. More to come.

Once the information files are loaded into **ABAP**Analyzer, you must create reports (based in the report templates) to display information that may include text, chart, images (among others).

## Development
To develop this plugin, the WordPress *wp.config.php* file has been updated with the following lines:

```php
define('WP_DEBUG', true);
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );
```