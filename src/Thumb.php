<?php
namespace Klevze\Thumb;

use Klevze\Thumb\Image;
use Klevze\Thumb\Exceptions\ReportException;

class Thumb
{
	private $image;

	public function __construct()
	{
	}

   	public function make(int $width, string $source, string $destination, int $quality = 100) : void
   	{
        if (!file_exists($source)) {
			throw new ReportException('Cannot open source image file.');	
        }

        $this->image = new Image($source);

        # Open main picture
    	$sourceImage = $this->openImage();

    	# Resize image
		$destinationImage = $this->resizeImage($width, $sourceImage);       

		# Save new image thumbnail
       	$this->storeImage($destinationImage, $destination, $quality);

       	# Clear image handlers
       	imagedestroy($sourceImage);
       	imagedestroy($destinationImage);
    }

    private function calcHeight(int $resizedWidth)
    {
    	$width = $this->image->getWidth();
    	$height = $this->image->getHeight();

		return intval($height * $resizedWidth / $width + 0.5);
    }

    private function openImage()
    {
    	$filename = $this->image->getFilename();
    	$mime = $this->image->getMime();

    	if ($mime == image_type_to_mime_type(IMAGETYPE_JPEG)) {
    		$image = imagecreatefromjpeg($filename);
    	}

    	if ($mime == "image/pjpeg") {
    		$image = gdImageimagecreatefromjpeg($filename);
    	}

    	if ($mime == image_type_to_mime_type(IMAGETYPE_PNG)) {
    		$image = imagecreatefrompng($filename);
    	}

    	if ($mime == image_type_to_mime_type(IMAGETYPE_GIF)) {
    		$image = imagecreatefromjpeg($filename);
    	}

    	if ($mime == image_type_to_mime_type(IMAGETYPE_WEBP)) {
    		$image = imagecreatefromweb($filename);
    	}

    	if (!$image) {
			throw new \Exception('Cannot open source image file.');	
    	}

    	return $image;
    }

    private function storeImage(&$newImage, $destination, $quality = 100)
    {
    	$filename = $this->image->getFilename();
    	$mime = $this->image->getMime();

    	if ($mime == image_type_to_mime_type(IMAGETYPE_JPEG)) {
    		imagejpeg($newImage, $destination, $quality);
    	}

    	if ($mime == "image/pjpeg") {
    		imagejpeg($newImage, $destination, $quality);
    	}

    	if ($mime == image_type_to_mime_type(IMAGETYPE_PNG)) {
    		imagepng($newImage, $destination);
    	}

    	if ($mime == image_type_to_mime_type(IMAGETYPE_GIF)) {
    		imagegif($newImage, $destination);
    	}

    	if ($mime == image_type_to_mime_type(IMAGETYPE_WEBP)) {
    		imagewebp($newImage, $destination, $quality);
    	}
    }

    private function resizeImage(int $width, &$sourceImage)
    {
    	$height = $this->calcHeight($width);

		$image = imagecreatetruecolor($width, $height);
       	
       	if (!$image) {
			throw new \Exception('Cannot create destination image file.');	       		
       	}

       	imagecopyresampled($image, 
       					   $sourceImage, 
       					   0, 
       					   0, 
       					   0, 
       					   0, 
       					   $width, 
       					   $height, 
       					   $this->image->getWidth(), 
       					   $this->image->getWidth()
       					  );

       	return $image;
    }

	public static function resizeAnimatedGIF(int $width, int $height, string $source, string $destination, int $percent = 100)
	{
		$percent = $percent * 100;
		$crop_w = 0;
		$crop_h = 0;
		$crop_x = 0;
		$crop_y = 0;

		$image = new \Imagick($source);
		$originalWidth = $image->getImageWidth();
		$originalHeight = $image->getImageHeight();

		$size_w = $width;
		$size_h = $height;
		
		if (($size_w - $originalWidth) > ($size_h - $originalHeight)) {
		   $s = $size_h / $originalHeight;
		   $size_w = round($originalWidth * $s);
		   $size_h = round($originalHeight * $s);
		} else {
		   $s = $size_w/$originalWidth;
		   $size_w = round($originalWidth * $s);
		   $size_h = round($originalHeight * $s);
		}

		$image = $image->coalesceImages();

		foreach ($image as $frame) {
		   $frame->cropImage($crop_w, $crop_h, $crop_x, $crop_y);
		   $frame->thumbnailImage($size_h, $size_w);
		   $frame->setImagePage($size_h, $size_w, 0, 0);
		}

		$imageContent = $image->getImagesBlob();
		
		$fp = fopen($destination,'w');
		fwrite($fp, $imageContent);
		fclose($fp);
	}
}