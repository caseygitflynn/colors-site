<?php

namespace Color\Adaptor;

class Jpeg implements ImageAdaptor
{

    public function getContentType()
    {
        return "image/jpeg";
    }

    public function getImageData($image)
    {
        ob_start();
        imagejpeg($image, null, 90);
        $imagedata = ob_get_contents();
        ob_end_clean();

        return $imagedata;
    }
}