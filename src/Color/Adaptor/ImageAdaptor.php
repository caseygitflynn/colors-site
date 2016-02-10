<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 2/10/16
 * Time: 5:58 PM
 */

namespace Color\Adaptor;

interface ImageAdaptor
{
    public function getContentType();
    public function getImageData($image);
}