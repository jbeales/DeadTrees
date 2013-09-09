=== DeadTrees ===
Contributors: johnnyb
Tags: books, reading, library, book, 
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Share the books you've read with your readers, family, & friends.  Never receive a gift of a book you've read again!

== Description ==

DeadTrees is a Wordpress plugin that allows you to share the books you've been reading with your readers.

It's slightly influenced by other "My Library" type of plugins, such as the whole Now Reading/Reloaded/Redux group of plugins, but is a complete re-write using Wordpress's Custom Post Type features.

I wrote DeadTrees because I wanted to be able to post to my blog when I read a book, but I didn't want to be required to actually write a post about the book if I didn't want to.

## How it works
To post a book that you read, you'll go to the admin of your site and choose "Books" from the main menu. You can then enter the title of the book, the author's name(s), Amazon's ASIN, (likely the ISBN), and, if you wish, you may write about the book, but you don't have to if you don't want to. 

Hit publish and the DeadTrees grab the cover art from Amazon and publish your book.

You can also tag the book, just like a post. The pool of tags is shared between posts & books.

Development is on GitHub at https://github.com/jbeales/DeadTrees

Report issues at https://github.com/jbeales/DeadTrees/issues

See the public side in action, with some custom templating: http://johnbeales.com/books/


== Installation ==

Either: 

1. Go to Plugins > Add New in your WordPress admin.
2. Install DeadTrees by johnnyb (this plugin)
3. Activate the plugin through the 'Plugins' menu in WordPress

OR:

1. Upload the folder `deadtrees` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Troubleshooting ==
If you get a 404 error when trying to view your first book post, visit the Permalinks page in the admin, (Settings > Permalinks), and please leave a comment on [Github issue #13](https://github.com/jbeales/DeadTrees/issues/13) or post in the support forums on wordpress.org to let me know that the problem isn't fixed yet.


== Screenshots ==

1. The main Books page in the WordPress admin
2. Editing a Book entry in the admin.
3. Detail on the Book Info box in the admin.  Only one ASIN is required, but you can enter one for each Amazon site if you wish.
4. This is the default affiliate link section in the Twenty Eleven theme.

== Changelog ==

= 1.0.4 =
* Fix bug where "Display Books On" setting didn't save properly, (this makes it so that books can be displayed in archives and on the main posts page).
* Fix a typo.

= 1.0.3 =
* Fix readme and plugin header file problems with 1.0.2 deployment.

= 1.0.2 =
* Fix issue where the same cover image would be shown for all books at wpurl/books/, ([GitHub Issue #10](https://github.com/jbeales/DeadTrees/issues/10))
* Fix issue where it was hard to turn off the auto-inclusion of the bookbox at the end of book entries, ([GitHub Issue #5](https://github.com/jbeales/DeadTrees/issues/5))
* Improve formatting of Installation section of readme
* Add debug functionality

= 1.0.1 =
* Updates plugin name, (one word only).
* Fixes CSS URLs
* Adds screenshots to wordpress.org
* Updates installation instructions

= 1.0 =
* Initial version of DeadTrees


== Upgrade Notice ==

= 1.0.1 =
Fixed paths so that styles display properly. Updated plugin name.

= 1.0 =
Initial Version of DeadTrees


