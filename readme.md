# Dead Trees
Dead Trees is a Wordpress plugin that allows you to share the books you've been reading with your readers.

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

## Installation
Install Dead Trees like a normal Wordpress plugin. It should work out of the box, but it'll work better if you set up a couple of extra things:

### Amazon Affiliate IDs
Currently Dead Trees can auto-generate affiliate links to Amazon.com, Amazon.ca, and Amazon.co.uk so that your readers can purchase books that you recommend, and you can receive a commission on sales from Amazon. Sign up for an affiliate ID at http://affiliate-program.amazon.com, (or .ca, or .co.uk, or all 3). Once you've got your affiliate IDs enter them in Settings > Dead Trees in your Wordpress admin.

### Amazon API Credentials
In order to grab a book's cover art from Amazon you need access to Amazon's Product Advertising API. It's free, and you can tie it to your existing Amazon account. Go to http://affiliate-program.amazon.com and sign in to your affiliate account, then click the "Product Advertising API" tab at the top and follow the directions to sign up. Once you are set up with the Product Advertising API enter your Key ID and Secret Key in Settings > Dead Trees in your Wordpress admin.

### LibraryThing API Key
The [LibraryThing CoverThing API](https://blog.librarything.com/main/2008/08/a-million-free-covers-from-librarything/) requires a free API key. 

#### Getting a LibraryThing API Key
- Sign Up for [LibraryThing](https://www.librarything.com/)
- Fill out the [Get a Developer Key](https://www.librarything.com/services/keys.php) form on LibraryThing.
- An API key will be E-mailed to you.

## Templating
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

## Troubleshooting
If you get a 404 error when trying to view your first book post, visit the Permalinks page in the admin, (Settings > Permalinks), and please leave a comment on [Github issue #13](https://github.com/jbeales/DeadTrees/issues/13) or post in the support forums on wordpress.org to let me know that the problem isn't fixed yet.