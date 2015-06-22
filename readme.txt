=== Posts by Category ===
Contributors: Shellbot
Tags: category, posts, shortcode, list, tag
Requires at least: 2.9
Tested up to: 4.2.2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=T3XBQZNXBEHPC
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a list of posts from a specific category or tag.

== Description ==

Posts by Category lets you display a list of posts pulled from a particular category or tag, and optionally
group them by year, month or first letter of the post title.

Current features include:

* Set a title to be displayed above list of posts
* Shortcode allows post list to be inserted anywhere
* Limit how many posts should be displayed
* Group posts by year, month or first letter

**Usage**

To display the list of posts, add the following shortcode to your post or page.

Default settings:

`[sb_category_posts]`

Custom settings:

`[sb_category_posts show="10" cat="3" group_by="year"]`

For full list of parameters see [the plugin release page](http://codebyshellbot.com/wordpress-plugins/posts_by_category/ "Posts by Category")

== Installation ==

1. Upload the 'posts-by-category' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the shortcode to your post or page
4. Enjoy!

== Frequently Asked Questions ==

= How do I find my category/tag ID number? =

When editing a category or tag, the URL in your browser will look something like this:
`yoursite.com/wp-admin/edit-tags.php?action=edit&taxonomy=category&tag_ID=25&post_type=post`

The number after tag_ID is your category or tag ID.

== Changelog ==

= 1.0 =
* First version