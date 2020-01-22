=== DeadTrees ===
Contributors: johnnyb
Tags: books, reading, library, book, 
Requires at least: 3.0
Requires PHP: 5.4
Tested up to: 5.3.2
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Share the books you've read with your readers, family, & friends.  Never again receive a book you've already read as a gift!

== Description ==

DeadTrees is a Wordpress plugin that allows you to share the books you've been reading with your readers.

It's slightly influenced by other "My Library" type of plugins, such as the whole Now Reading/Reloaded/Redux group of plugins, but is a complete re-write using Wordpress's Custom Post Type features.

I wrote Dead Trees because I want to post on my blog when I read a book, but only sometimes want to write about the book. Dead Trees lets me, (and you!), do that.

## New in Version 1.1: Multiple Book Cover Sources

In version 1.1 book covers will be fetched from [OpenLibrary.org](https://openlibrary.org/), [LibraryThing](https://www.librarything.com/), or Amazon, depending on the settings, and what's available where.

If you have set your Amazon API credentials, you can choose to look for a cover at Amazon either first or last: first for people who are working on building affiliate income from Amazon, and have plenty of [API quota](https://docs.aws.amazon.com/AWSECommerceService/latest/DG/TroubleshootingApplications.html#efficiency-guidelines), or last for people who prefer to promote independent book sources or can't or don't want to use Amazon's services.

For non-Amazon book covers, OpenLibrary.org is checked first as it provides larger images than other options. If OpenLibrary.org doesn't have a cover for a book and you have set a LibraryThing API key we will fall back to looking for a cover at LibraryThing.

OpenLibrary.org does not require any API credentials or any special configuration.

## How it works
To post a book that you read, you'll go to the admin of your site and choose "Books" from the main menu. You can then enter the title of the book, the author's name(s), ISBN, Amazon's ASIN, (likely the ISBN), and, if you wish, you may write about the book, but you don't have to if you don't want to. 

Hit publish and the DeadTrees will grab the cover art from Open Library or Amazon and publish your book.

You can also tag the book, just like a post. The pool of tags is shared between posts & books.

Development is on GitHub at https://github.com/jbeales/DeadTrees

Report issues at https://github.com/jbeales/DeadTrees/issues

See the public side in action, with some custom templating: https://johnbeales.com/books/

## Thanks

Thanks to OpenLibrary.org and LibraryThing for providing open book cover data. APIs like these that share information make the independent web possible and fun. 


== Installation ==

= 1. Install the Plugin = 
Either:  

1. Go to Plugins > Add New in your WordPress admin.
2. Install DeadTrees by johnnyb (this plugin)
3. Activate the plugin through the 'Plugins' menu in WordPress

OR:

1. Upload the folder `deadtrees` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


= 2. (Optional) Add API Keys & Affiliate IDs =

1. **Amazon Affiliate IDs**
Currently Dead Trees can auto-generate affiliate links to Amazon.com, Amazon.ca, and Amazon.co.uk so that your readers can purchase books that you recommend, and you can receive a commission on sales from Amazon. Sign up for an affiliate ID at http://affiliate-program.amazon.com, (or .ca, or .co.uk, or all 3). Once you've got your affiliate IDs enter them in Settings > Dead Trees in your Wordpress admin.

2. **Amazon API Credentials**
In order to grab a book's cover art from Amazon you need access to Amazon's Product Advertising API. It's free, and you can tie it to your existing Amazon account. Go to http://affiliate-program.amazon.com and sign in to your affiliate account, then click the "Product Advertising API" tab at the top and follow the directions to sign up. Once you are set up with the Product Advertising API enter your Key ID and Secret Key in Settings > Dead Trees in your Wordpress admin.

3. **LibraryThing API Key**
The [LibraryThing CoverThing API](https://blog.librarything.com/main/2008/08/a-million-free-covers-from-librarything/) requires a free API key. 

= Getting a LibraryThing API Key =

1. Sign Up for [LibraryThing](https://www.librarything.com/)
2. Fill out the [Get a Developer Key](https://www.librarything.com/services/keys.php) form on LibraryThing.
3. An API key will be E-mailed to you.


== Troubleshooting ==
If you get a 404 error when trying to view your first book post, visit the Permalinks page in the admin, (Settings > Permalinks), and please leave a comment on [Github issue #13](https://github.com/jbeales/DeadTrees/issues/13) or post in the support forums on wordpress.org to let me know that the problem isn't fixed yet.

== Templating ==

## Template Tags
Dead Trees creates some template tags for you. Here are the important ones. If you want to see the not-so-important ones look in template_tags.php, they're all there:

### dt_bookbox()
Displays a box with the book's cover art and links to buy it from Amazon.com, Amazon.ca, and Amazon.co.uk.

Call dt_bookbox() inside the loop or pass it the post ID of a book.

You can completely customize the output of dt_bookbox() by creating a file called deadtree-bookbox.php in your theme. You might want to copy deadtrees/template/deadtree-bookbox.php as a starting point.

### dt_get_amazon_url($domain='amazon.com', $post_id=0)
Gets the Amazon affiliate URL for a book.
Choose which site to link to with the first argument, (can be 'amazon.com' 'amazon.ca' or 'amazon.co.uk', defaults to 'amazon.com'). If called in the loop the second argument is not required, but if you want to call this outside of the loop provide the post ID of a book as the second argument.

### dt_get_bookbox_image($post_id=0)
Gets the cover art for a book. If called in the loop this function can, (and should), be called with no arguments. Uses wp_get_attachment_image() to get the image.

### dt_get_bookbox_comment($post_id=0)
Gets an extra comment to display in the bookbox. You enter this in the admin in the box that you put the ASIN into. This should be called with no arguments in the loop, or passed the post ID of a book outside of the loop.

## Template Files
Dead Trees introduces a new post type: dt_book, and a new taxonomy: dt_writer. This means that you can put a file into your theme called type-dt_book.php and it'll be used in place of single.php, and you can add taxonomy-dt_writer.php and it'll be used in place of tag.php. This way you can make your reading list look like a real library!

## On Caching Plugins
If you use WP Super Cache, you'll need to clear your cache, or turn off caching, when updating the design of the bookbox, (updating deadtree-bookbox.php), and when updating your Amazon Affiliate IDs. Once your changes are made just make sure the cache is clear and turn caching back on.

I'm not sure about other caching plugins, but I suspect they'll behave in a similar manner.


== Screenshots ==

1. The main Books page in the WordPress admin
2. Editing a Book entry in the admin.
3. Detail on the Book Info box in the admin.  Only one ASIN is required, but you can enter one for each Amazon site if you wish.
4. This is the default affiliate link section in the Twenty Eleven theme.

== Changelog ==

= 1.1.1 & 1.1.2 = 
* Tweak deployment & appearance in WordPress plugin repository.

= 1.1 =
* Add: [Open Library](https://openlibrary.org/) and [LibraryThing](https://www.librarything.com) as sources of book covers.
* Change: set Amazon configurable as the first or last source of book covers.
* Fix: Fixed bug where sometimes wrong Amazon Affiliate Tag would be sent to Amazon Product Advertising API when fetching book covers.

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

= 1.1 =
Add non-Amazon screenshot options. Update Amazon screenshot fetching.

= 1.0.1 =
Fixed paths so that styles display properly. Updated plugin name.

= 1.0 =
Initial Version of DeadTrees


