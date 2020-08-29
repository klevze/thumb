<?php
namespace Klevze\Thumb;

class Image
{
	private $width;
	private $height;
	private $filename;
	private $extension;
	private $mime_content_type;

	public function __construct(string $filename)
	{
		if (!file_exists($filename)) {
			throw new Exception('Filename doesnt exists.');	
		}

		$this->filename = $filename;
		$this->getImageSize();
		$this->getImageExtension();
		$this->getImageMime();
	}

	public function getMime()
	{
		return $this->mime_content_type;
	}

	public function getFilename()
	{
		return $this->filename;
	}

	public function getHeight()
	{
		return $this->height;
	}
	
	public function getWidth()
	{
		return $this->width;
	}

	private function getImageSize()
	{
		$img_info = getimagesize($this->filename);
		$this->width = $img_info[0];
		$this->height = $img_info[1];
	}

	private function getImageMime()
	{
		$this->mime_content_type = mime_content_type($this->filename);
	}

	private function getImageExtension()
	{
		$this->extension = pathinfo($this->filename, PATHINFO_EXTENSION);
	}
}