<?php
namespace Color;

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
        $rgb = $this->color->getRGB();

        $fillColor = imagecolorallocate($this->image, $rgb["r"], $rgb["g"], $rgb["b"]);
        imagefill($this->image, 0, 0, $fillColor);

        ob_start();

        if ($this->extension == "jpg" || $this->extension == "jpeg") {
            imagejpeg($this->image);
        }
        if ($this->extension == "png") {
            imagepng($this->image);
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

        return null;
    }
}