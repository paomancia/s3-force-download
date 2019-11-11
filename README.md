# S3 Force Download WordPress Plugin

**Contributors:** Paola Mancía
**Tags:** downloads, amazon, s3, amazon s3, bucket, media, cdn, cloudfront  
**Requires at least:** PHP >= 5.5
**License:** GPLv3

Programatically force browsers to download files from an S3 bucket.

## Contents

The S3 Force Download plugin includes the following files:

- An `includes` directory that contains the source code - a fully executable WordPress plugin.
- `s3-force-download.php`. The autoloaders and class initializations
- `CHANGELOG.md`. The list of changes to the core project.
- `README.md`. The file that you’re currently reading.

## Features

- Provides a shortcode to easily create `download links` with dynamic markup.
- Provides a shortcode to create a `download page`.
- Provides a way of specifying the type of files in your Amazon S3 bucket you wish to force browsers to download instead of just opening.

## Getting Started

1. **Sign up for AWS** – Before you begin, you need to sign up for an AWS account and retrieve your [AWS credentials][docs-signup].

2. **Minimum requirements** – To run the Amazon SDK, your system will need to meet the minimum requirements, including having **PHP version 5.5 or higher** due to restrictions set by Amazon.

3. **Enter your Amazon S3 Bucket information in the Settings page** – This plugin assumes you have connected your WordPress site with your S3 bucket already.

After activating this plugin, go to your Admin Dashboard and find the `S3 Force Download` menu option under Settings. Fill in all the required fields:

- `Bucket Name`
- `Bucket Region`
- `S3 Access Key`
- `S3 Secret Key`
- `S3 file path`

If you prefer to set environment variables instead, you can leave almost all of these fields empty except the `S3 file path` field.

These should be the names of your environment variables:

- `S3_BUCKET_NAME`
- `S3_BUCKET_REGION`
- `S3_ACCESS_KEY`
- `S3_SECRET_KEY`

The following file extensions are the default ones allowed for download: `'pdf', 'mp4', 'mp3', 'jpg', 'png'` but you can set specific file types you wish to support instead. This list should be entered in the `Allowed File Extensions` field and should be comma separated. Example:

```
pdf, jpg, jpeg, png, mp4
```

4. **Create a Download Page** – From the Admin Dashboard, create a new page which will be your download page. The status of this new page can be either Public or Private since any user that tries to access this page directly in the URL will be redirected to the homepage of your site. In the content area of this new page, paste the shortcode `[sfd_page]` or call the function `do_shortcode()` from your theme's template:

```php
<?php echo do_shortcode('[sfd_page]'); ?>
```

5. **Create a Download Link** – You can create as many download links as you want, all you have to do is use the shortcode `[sfd_link]` with the 2 required attributes: `page_slug` and `file_id`. Example: `[sfd_link page_slug="name-of-download-page" file_id="wordpress-id-of-media-file"]`

List of all attributes supported:

- `page_slug`: Required. The link will point to this newly created download page.
- `file_id`: Required. The WordPress id of the media file you'd like to force download.
- `classes`: Optional. The CSS classes you'd like to be applied to the link's HTML element.
- `data_attr`: Optional. A custom data attribute you'd like to embed on the link's HTML element.
- `data_val`: Optional. The corresponding value for the custom data attribute set above.

## Quick Examples

### Creating a Link in a Post

```
[sfd_link page_slug="download" file_id="2476" classes="btn btn-primary" data_attr="modalid" data_val="2"]
    <i class="fa fa-download" aria-hidden="true"></i>
    Download
[/sfd_link]
```

### Creating a Link in a Template with PHP code

```php
<?php

echo do_shortcode( '[sfd_link page_slug="download" file_id="' . $file_id . '" classes="d-inline-flex flex-nowrap justify-content-center" data_attr="modalid" data_val="' . $moodal_id . '" ]' . '<i class="fa fa-download" aria-hidden="true"></i>' . $btn_text . '[/sfd_link]' );

?>
```

## Includes

Note that if you include your own classes or third-party libraries, these are the locations in which said files may go:

- `includes/SFD` is for all admin-specific and core functionality
- `includes/vendor` is for 3rd party libraries such as AWS SDK for PHP
