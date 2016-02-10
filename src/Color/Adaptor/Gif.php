<?php
namespace Color\Adaptor;

class Gif implements ImageAdaptor
{

    public function getContentType()
    {
        return "image/gif";
    }

    public function getImageData($image)
    {
        ob_start();
        imagegif($image);
        $imagedata = ob_get_contents();
        ob_end_clean();

        return $imagedata;
    }
}