<?php
namespace Color;

use Slim\Slim;

class Image
{
    protected $color;

    protected $width;

    protected $height;

    protected $extension;

    protected $image;

    /**
     * Image constructor.
     * @param Color $color
     * @param int $width
     * @param int $height
     * @param string $extension
     */
    public function __construct(Color $color, $width = 500, $height = 500, $extension = "jpg")
    {
        $this->color = $color;
        $this->width = $width;
        $this->height = $height;
        $this->extension = $extension;
    }

    public function getImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $fillRGB = $this->color->getRGB();

        $fillColor = imagecolorallocate($this->image, $fillRGB["r"], $fillRGB["g"], $fillRGB["b"]);
        imagefill($this->image, 0, 0, $fillColor);

        $this->setText($this->color->getHexColor(), $this->color->getContrastColor());

        ob_start();

        if ($this->extension == "jpg" || $this->extension == "jpeg") {
            imagejpeg($this->image, null, 90);
        }
        if ($this->extension == "png") {
            imagepng($this->image);
        }

        if ($this->extension == "gif") {
            imagegif($this->image);
        }

        $imagedata = ob_get_contents();
        ob_end_clean();

        return $imagedata;
    }

    public function getContentType()
    {
        if ($this->extension == "jpg" || $this->extension == "jpeg") {
            return "image/jpeg";
        }

        if ($this->extension == "png") {
            return "image/png";
        }

        if ($this->extension == "gif") {
            return "image/gif";
        }

        return null;
    }

    protected function setText($text = "", Color $color)
    {
        $textRGB = $color->getRGB();
        $textColor = imagecolorallocate($this->image, $textRGB["r"], $textRGB["g"], $textRGB["b"]);
        $fontSize = min($this->width, $this->height) / strlen($text) / 1.5;
        $xPos = $this->width / 2 - ($fontSize * strlen($text) / 2);
        $yPos = $fontSize / 2 + $this->height / 2;
        imagettftext($this->image, $fontSize, 0, $xPos, $yPos, $textColor, $this->font(), $text);
    }

    protected function font()
    {
        $root = Slim::getInstance('default')->root();
        $path = $root . 'webfonts/Monaco.ttf';

        return $path;
    }
}