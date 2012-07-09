=== Plugin Name ===
Contributors: johnnyb
Tags: books, reading, library, book, 
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Share the books you've read with your readers, family, & friends.  Never receive a gift of a book you've read again!

== Description ==

Dead Trees is a Wordpress plugin that allows you to share the books you've been reading with your readers.

It's slightly influenced by other "My Library" type of plugins, such as the whole Now Reading/Reloaded/Redux group of plugins, but is a complete re-write using Wordpress's Custom Post Type features.

I wrote Dead Trees because I wanted to be able to post to my blog when I read a book, but I didn't want to be required to actually write a post about the book if I didn't want to.

## How it works
To post a book that you read, you'll go to the admin of your site and choose "Books" from the main menu. You can then enter the title of the book, the author's name(s), Amazon's ASIN, (likely the ISBN), and, if you wish, you may write about the book, but you don't have to if you don't want to. 

Hit publish and the Dead Trees grab the cover art from Amazon and publish your book.

You can also tag the book, just like a post. The pool of tags is shared between posts & books.

Development is on GitHub at https://github.com/jbeales/DeadTrees

Report issues at https://github.com/jbeales/DeadTrees/issues


== Installation ==

Either: 
1. Go to Plugins > Add New in your WordPress admin.
2. Install Dead Trees by johnnyb (this plugin)
3. Activate the plugin through the 'Plugins' menu in WordPress

OR:

1. Upload the folder `deadtrees` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

THEN:

1. Place `<?php dt_bookbox(); ?>` where you want book information to appear.



== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)
2. This is the second screen shot

== Changelog ==

= 1.0 =
* Initial version of Dead Trees


== Upgrade Notice ==

= 1.0 =
Initial Version of Dead Trees


