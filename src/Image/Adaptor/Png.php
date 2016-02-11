<?php

namespace Image\Adaptor;

class Png implements ImageAdaptor
{

    public function getContentType()
    {
        return "image/png";
    }

    public function getImageData($image)
    {
        imagepng($image);
    }
}