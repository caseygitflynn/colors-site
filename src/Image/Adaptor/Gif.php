<?php
namespace Image\Adaptor;

class Gif implements ImageAdaptor
{

    public function getContentType()
    {
        return "image/gif";
    }

    public function getImageData($image)
    {
        imagegif($image);
    }
}