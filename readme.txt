=== Template List Metabox ===
Contributors: 010Pixel
Tags: metabox, template, custom post types, post template, plugin, 010pixel
Requires at least: 3.1
Tested up to: 3.9.1
Stable tag: 1.1.0
License: GNU General Public License, version 3 (GPL-3.0)

Template list metabox is to create Metabox for Dropdown List of all available Templates to attach with any Custom Post Types

== Description ==
This plugin will allow you to select custom posts types for which you want to show metabox of template list dropdown menu.

On the front end, this plugin will help to show selected template for the post.

Now you can choose to have different templates for each post just like pages.

**Made by [010Pixel](http://www.010pixel.com/)**


== Installation ==
1. Unpack the download-package
2. Upload folder include all files to the `/wp-content/plugins/` directory. The final directory tree should look like `/wp-content/plugins/010pixel-template-list-metabox/010pixel_tmplt_list_metabox.php`
3. Activate the plugin through the `Plugins` menu in WordPress
4. Administrator can go to `Plugins` > `Template List Metabox` menu and configure the plugin (Type of Posts/Custom Posts where you want to show Template List Metabox)

* or use the automatic install via backend of WordPress


== Frequently Asked Questions ==
= How to choose which Custom Post Types can have the template list metabox? =
Go to `Plugins` > `Template List Metabox`. You will be able to see all the custom post types including `Post`.
Tick the custom post type for which you want to show template list metabox and click `Submit`. You are done!

= How to select custom template for post? =
Enable Template List Metabox by following the method shown above and you will be able to see template list metabox when you create a post.
This will list all the templates from your theme.
Just choose the template which you want to show for a particular post and the post will have that template attached to it.

= What if I just want to show the template of single.php or single-{custom-post-type}.php? =
Select 'Default Template' and save the post.

= I am not able to see the metabox and the post is not selecting custom template on front end, what to do? =
Check if the plugin is Deactivated.
Check if the post-type of the post -you are trying to attach template to- might not be selected in `Plugin` > `Template List Metabox` page.


== Screenshots ==
1. Settings in WordPress to choose Custom Post Types
2. Sample Template List Metabox


== Use ==
Before using this plugin, you should have some templates ready to be used.

1. Go to `Plugins` > `Template List Metabox`. You will be able to see all the custom post types including `Post`.
2. Tick all the checkbox for the post types for which you want to enable template list metabox.
3. Create a post and choose the template you would like to use.
4. If you do not want to choose custom template then select `Default Template`. Choosing `Default Template` will show content from single.php, single-{$object->post_type}.php, single-{$object->post_type}-{$object->post_name}.php

For any query, you can contact me at [010 Pixel](http://www.010pixel.com/)


== Changelog ==
= v1.0.0 =
* Initial release.

= v1.1.0 =
* Removed bug of settings link not displaying in plugin list
* Added setting page under Settings menu instead of Plugins menu


= v1.1.1 =
* Removed bug of settings link showing error page