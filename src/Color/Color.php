<?php

namespace Color;

/**
 * Color utility and conversion
 *
 * Represents a color value, and converts between RGB/HSV/XYZ/Lab
 *
 * Example:
 * $color = new Color(0xFFFFFF);
 *
 * @author Harold Asbridge <hasbridge@gmail.com>
 */
class Color
{
    protected $hexColor;

    /**
     * Color constructor.
     * @param $hexColor
     */
    public function __construct($hexColor = null)
    {
        if ($hexColor != null) {
            $this->setHexColor($hexColor);
        }
    }

    public static function random_color()
    {
        $instance = new static();
        $instance->setHexColor($instance->random_color_part() . $instance->random_color_part() . $instance->random_color_part());

        return $instance;
    }

    /**
     * @return mixed
     */
    public function getHexColor($withHash = true)
    {
        if ($withHash) {
            return "#" . strtoupper($this->hexColor);
        } else {
            return strtoupper($this->hexColor);
        }
    }

    /**
     * @param mixed $hexColor
     */
    public function setHexColor($hexColor)
    {
        if (preg_match("/([a-fA-F0-9]{3}){1,2}\b/", $hexColor)) {
            $this->hexColor = $hexColor;
        } else {
            throw new \InvalidArgumentException("Invalid hex color #" . $hexColor);
        }
    }

    public function getContrastColor()
    {
        $r = hexdec(substr($this->hexColor,0,2));
        $g = hexdec(substr($this->hexColor,2,2));
        $b = hexdec(substr($this->hexColor,4,2));
        $yiq = (($r*299)+($g*587)+($b*114))/1000;

        $contrastHex = ($yiq >= 128) ? '000000' : 'FFFFFF';

        return new Color($contrastHex);
    }

    public function getRGB()
    {
        $r = hexdec(substr($this->hexColor,0,2));
        $g = hexdec(substr($this->hexColor,2,2));
        $b = hexdec(substr($this->hexColor,4,2));

        return [
            "r" => $r,
            "g" => $g,
            "b" => $b
        ];
    }

    private static function random_color_part()
    {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
}