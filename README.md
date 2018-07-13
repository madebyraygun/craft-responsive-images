![Craft Responsive Images](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require gentsagency/craft-responsive-images

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Responsive Images.

## Overview

In short: generate a bunch of images in different widths, throw them together in a `srcset` attribute, set a `sizes` attribute and let the browser handle the rest.

It generates images for these widths: 256px, 384px, 512px, 768px, 1024px, 1280px, 1536px, 1760px, 2560px.

### Features

 - Basic features work with Craft's built-in Image Transforms
 - Support for [imgix](https://www.imgix.com)
 - Integrates with Craft [AWS S3](https://github.com/craftcms/aws-s3)
 - Supports focal point

### Imgix Integration

 - Supports multiple Imgix sources (one per S3 bucket)
 - Supports secure URL signing
 - Supports master image purging

## Configuration

If you want to leverage [imgix](https://www.imgix.com), you'll have to configure a [AWS S3](https://github.com/craftcms/aws-s3) Asset Volume and create an imgix source for it. In the plugin settings, you can configure the Imgix domain, API key (for purging) and signing token (for secure URL's).

## Usage

For usage with Image fields:

```twig
{% set image = responsiveImage(entry.posterImage.one, { aspectRatio: 21/9 }) %}
<img src="{{ image.src }}" srcset="{{ image.srcset }}" sizes="100vw">
```

You can pass a width to `{{ img.src(640) }}` to change what size will be used as the fallback `src`. This might come in handy if you want to support IE11 and below without serving them blurry images. 

There is also a filter for HTML strings & Redactor fields:

```twig
{{ entry.story|responsiveImages({ aspectRatio: 21/9, sizes: '50vw'}) }}
```
