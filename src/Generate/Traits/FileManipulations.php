<?php namespace Aligilani\AdminGenerator\Generate\Traits;

use Illuminate\Support\Facades\File;

trait FileManipulations {

    /**
     * @param $fileName
     * @param $ifExistsRegex
     * @param $find
     * @param $replaceWith
     * @return null
     */
    protected function strReplaceInFile($fileName, $ifExistsRegex, $find, $replaceWith) {
        $content = File::get($fileName);
        if (preg_match($ifExistsRegex, $content)) {
            return;
        }

        return File::put($fileName, str_replace($find, $replaceWith, $content));
    }

}
