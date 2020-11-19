# Hide Author Archive

Contributors: kuno1, Takahashi_Fumiki  
Tags: permalink, author, archive, url  
Requires at least: 5.0  
Requires PHP: 5.6  
Tested up to: 5.5  
Stable tag: %nightly%  
License: GPLv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Hide author archive of WordPress.

## Description

This plugin will hide author's archive.
If your don't need author parameter, this plugin may enhance your site's security from bot inspection.

1. No more `?author=1` redirection to `/author/admin`
2. Ignore query var like `author=1` or `author_name=admin`
3. Hide user list in REST API from user without permission `list_users`. This capability is filterable.

## Installation

Search "hide author archives" in admin screen.
Altenatively, you can install it manually like below:

1. Upload `hide-author-archive` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions

### How To Contribute

We host plugin on [github](https://github.com/kuno1/hide-author-archive) and any issues and pull requests are welcomed!

## Changelog

### 1.1.0

* Support REST API.

### 1.0.0

* First release
