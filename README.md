# Assets loader
The developer tool, which helps to download image assets for your local dev site by a specific URL.

### How to start?
All we need is to clone current directory and run `composer install`;

### Usage
Let's imagine that you have some site on stage / prod environment. And you want to get the missing image assets for some page. For such purpose, you can run the next command:
`php -f process.php <url> <remote_docroot> <local_docroot>`

where:
 - **<url>** - a page URL you want to download assets from;
 - **<remote_docroot>** - the base directory on your remote site you want to download assets from;
 - **<local_docroot>** - the directory which will keep the assets (it should be writable);

E.g. the command below will download all image assets from https://www.example.com page (which are kept in `sites/default/files`) to your local site directory `/var/www/example.loc/sites/default/files` (the assets directory tree will be kept):
`php -f process.php https://www.example.com sites/default/files /var/www/example.loc/sites/default/files`
