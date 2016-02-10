<?php
namespace Color;

use Slim\Slim;

class Image
{
    protected $color;

    protected $width;

    protected $height;

    protected $text;

    protected $adaptor;

    protected $image;

    protected static $extensions = ["jpg", "jpeg", "png", "gif"];

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
        $this->text = strtoupper($text);
        if (!in_array($extension, $this::$extensions)) {
            throw new \InvalidArgumentException("Invalid image type " . $extension);
        } else {
            $adaptor = '\\Color\\Adaptor\\' . ucwords($extension);
            $this->adaptor = new $adaptor;
        }
    }

    public function getImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $fillRGB = $this->color->getRGB();

        $fillColor = imagecolorallocate($this->image, $fillRGB["r"], $fillRGB["g"], $fillRGB["b"]);
        imagefill($this->image, 0, 0, $fillColor);

        $this->setText($this->text, $this->color->getContrastColor());

        return $this->adaptor->getImageData($this->image);
    }

    public function getContentType()
    {
        return $this->adaptor->getContentType();
    }

    protected function setText($text = "", Color $color)
    {
        $textRGB = $color->getRGB();
        $textColor = imagecolorallocate($this->image, $textRGB["r"], $textRGB["g"], $textRGB["b"]);
        $fontSize = $this->width / strlen($text) / 1.5;
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