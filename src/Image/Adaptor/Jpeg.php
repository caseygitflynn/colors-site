<?php

namespace Image\Adaptor;

class Jpeg implements ImageAdaptor
{

    public function getContentType()
    {
        return "image/jpeg";
    }

    public function getImageData($image)
    {
        imagejpeg($image, null, 90);
    }
}