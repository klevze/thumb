# Thumb
##simple thumbnail generator for Laravel

- Generates Thumbnail (image) from a given image file (jpg, png, gif, webp)
- This uses GD.

[View on Packagist](https://packagist.org/packages/klevze/thumb)


## Installation

`composer require klevze/thumb`


## How to use

```php
use Thumb;


Thumb::make($width, $source_image_name, $output_image_name, $quality);

```

