<?php

namespace Color\Adaptor;

class Png implements ImageAdaptor
{

    public function getContentType()
    {
        return "image/png";
    }

    public function getImageData($image)
    {
        ob_start();
        imagepng($image);
        $imagedata = ob_get_contents();
        ob_end_clean();

        return $imagedata;
    }
}