# Dead Trees
Dead Trees is a Wordpress plugin that allows you to share the books you've been reading with your readers.

It's slightly influenced by other "My Library" type of plugins, such as the whole Now Reading/Reloaded/Redux group of plugins, but is a complete re-write using Wordpress's Custom Post Type features.

I wrote Dead Trees because I wanted to be able to post to my blog when I read a book, but I didn't want to be required to actually write a post about the book if I didn't want to.

## How it works
To post a book that you read, you'll go to the admin of your site and choose "Books" from the main menu. You can then enter the title of the book, the author's name(s), Amazon's ASIN, (likely the ISBN), and, if you wish, you may write about the book, but you don't have to if you don't want to. 

Hit publish and the Dead Trees grab the cover art from Amazon and publish your book.

You can also tag the book, just like a post. The pool of tags is shared between posts & books.

## Installation
Install Dead Trees like a normal Wordpress plugin. It'll work out of the box, but it'll work better if you set up a couple of extra things:

### Amazon Affiliate IDs
Currently Dead Trees can auto-generate affiliate links to Amazon.com, Amazon.ca, and Amazon.co.uk so that your readers can purchase books that you recommend, and you can receive a commission on sales from Amazon. Sign up for an affiliate ID at http://affiliate-program.amazon.com, (or .ca, or .co.uk, or all 3). Once you've got your affiliate IDs enter them in Settings > Dead Trees in your Wordpress admin.

### Amazon API Credentials
In order to grab a book's cover art from Amazon you need access to Amazon's Product Advertising API. It's free, and you can tie it to your existing Amazon account. Go to http://affiliate-program.amazon.com and sign in to your affiliate account, then click the "Product Advertising API" tab at the top and follow the directions to sign up. Once you are set up with the Product Advertising API enter your Key ID and Secret Key in Settings > Dead Trees in your Wordpress admin.

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

## On Caching Plugins
If you use WP Super Cache, you'll need to clear your cache, or turn off caching, when updating the design of the bookbox, (updating deadtree-bookbox.php), and when updating your Amazon Affiliate IDs. Once your changes are made just make sure the cache is clear and turn caching back on.

I'm not sure about other caching plugins, but I suspect they'll behave in a similar manner.