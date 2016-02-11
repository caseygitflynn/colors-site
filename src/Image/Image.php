<?php

namespace Image;

use Color\Color;

class Image
{
    protected $color;

    protected $width;

    protected $height;

    protected $text;

    protected $adaptor;

    protected $image;

    /**
     * Image constructor.
     * @param Color $color
     * @param int $width
     * @param int $height
     * @param string $text
     * @param string $extension
     */
    public function __construct(Color $color, $width = 500, $height = 500, $text = "", $extension = "jpg")
    {
        $this->color = $color;
        $this->width = $width;
        $this->height = $height;
        $this->text = $text;
        $adaptor = __NAMESPACE__ . '\\Adaptor\\' . ucwords($extension);
        if (!class_exists($adaptor)) {
            throw new \InvalidArgumentException("Invalid image type " . $extension);
        } else {
            $this->adaptor = new $adaptor();
        }
    }

    public function getImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $fillRGB = $this->color->getRGB();

        $fillColor = imagecolorallocate($this->image, $fillRGB["r"], $fillRGB["g"], $fillRGB["b"]);
        imagefill($this->image, 0, 0, $fillColor);

        $this->setText($this->text, $this->color->getContrastColor());

        return $this->getImageData($this->image);
    }

    protected function getImageData($image)
    {
        ob_start();
        $this->adaptor->getImageData($image);
        $imagedata = ob_get_contents();
        ob_end_clean();

        return $imagedata;
    }

    public function getContentType()
    {
        return $this->adaptor->getContentType();
    }

    protected function setText($text = "", Color $color)
    {
        $textRGB = $color->getRGB();
        $textColor = imagecolorallocate($this->image, $textRGB["r"], $textRGB["g"], $textRGB["b"]);

        $fontSize = $this->estimateFontSize($text);

        $boundingBox = imageftbbox($fontSize, 0, $this->font(), $text);
        $textWidth = $boundingBox[2] - $boundingBox[0];
        $textHeight = $boundingBox[7] - $boundingBox[1];

        $xPos = ($this->width / 2) - ($textWidth / 2);
        $yPos = ($this->height / 2) - ($textHeight / 2);

        imagettftext($this->image, $fontSize, 0, $xPos, $yPos, $textColor, $this->font(), $text);
    }

    protected function estimateFontSize($text)
    {
        $boundingBox = imageftbbox(20, 0, $this->font(), $text);
        $textWidth = $boundingBox[2] - $boundingBox[0];
        $scale = ($this->width * 0.75) / $textWidth;

        return 20 * $scale;
    }

    protected function font()
    {
        $rc = new \ReflectionClass(get_class($this));
        $path = dirname($rc->getFileName()) . '/Monaco.ttf';

        return $path;
    }
}