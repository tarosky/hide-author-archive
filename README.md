# Hide Author Archive

Contributors: kuno1, Takahashi_Fumiki, tarosky, nevnum  
Tags: permalink, author, archive, url  
Requires at least: 5.9  
Requires PHP: 7.4  
Tested up to: 6.7  
Stable tag: %nightly%  
License: GPLv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Hide author archive URL of WordPress.

## Description

This plugin will hide author's archive.
If your don't need author parameter, this plugin may enhance your site's security from bot inspection.

1. No more `?author=1` redirection to `/author/admin`
2. Ignore query var like `author=1` or `author_name=admin`
3. Hide user list in REST API from user without permission `list_users` or `edit_others_posts`. This capability is filterable.

<pre>
/**
 * Filter capabilities to see author list.
 *
 * @param string[] $caps List of capabilities.
 */
add_filter( 'hide_author_archive_rest_query_capability', function( $caps ) {
	$caps[] = 'read';
	return $caps;
} );
</pre>

4. Remove users sitemap (`/wp-sitemap-users-1.xml`) from core XML sitemaps. Author archive URLs reveal `user_nicename`, which is usually derived from login name.

Besides that, this plugin tries to remove author information from meta tags and JSON-LD as possible as it can.

- Override author section in oEmbed.
- Replace author archive links (e.g. author name link in byline) with the posts page, because the link contains `user_nicename`. You can change the URL in Settings > Reading.
- Remove `comment-author-{user_nicename}` class from registered user's comments.
- Replace author name in feeds (`<dc:creator>` in RSS, `<author>` in Atom) with the site name. Author's URL is also removed from Atom feed. Display name is the same as login name by default, so feeds may reveal it.

Some organization needs to hide author information of each article.

### Supported Plugins

#### Yoast

1. Remove <code>meta=author</code>
2. Override author section in Article scheme in JSON-LD
3. Hide author section in Slack sharing.
4. Hide <code>article:author</code> in OGP.
5. Exclude all authors from XML sitemap.

## Installation

Search "hide author archives" in admin screen.
Altenatively, you can install it manually like below:

1. Upload `hide-author-archive` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions

### There is still author name exposed!

This plugin is for sites that **do not intend** to show author information, but WordPress exposes it unintentionally.
Its scope is machine-readable identifiers such as `user_nicename` (usually the same as the login name) in URLs, CSS classes, sitemaps, feeds and REST API, which site owners rarely notice.

The author's **display name** shown by your theme (e.g. `the_author()` in a byline) is out of scope, because the theme and the site owner intentionally output it.
If your theme shows author names you don't want, change the theme or set a proper display name for each user (by default, display name is the same as login name).

Our fundamental goal is covering Core features.
But feel free to let us know and request new features.

### How To Contribute

We host plugin on [github](https://github.com/tarosky/hide-author-archive) and any issues and pull requests are welcomed!

## Changelog

### 1.2.1

* Add some more filters for Yoast and oEmbed(Core).

### 1.2.0

* Add override function for Yoast.

### 1.1.5

* Hide single REST API for single user get `wp/v2/users/1`.
* Editor can see REST API users get to change author of posts.

### 1.1.1

* Support REST API.
* Fix coding standards.

### 1.0.0

* First release
