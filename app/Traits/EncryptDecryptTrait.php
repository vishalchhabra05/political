<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;
  
trait EncryptDecryptTrait {
  
    public function getAttribute($key)
    {
        log::debug('get attribute');
        $value = parent::getAttributeValue($key);
        log::debug('$key '. $key);
        log::debug('gsfgfg '. $value);

        if($value != null){
            if (in_array($key, $this->encryptdecrypttrait)) {
                log::debug('aaaaaa fdgf'. Crypt::decrypt($value));
                return $value = Crypt::decrypt($value);
            }
        }
        return $value;
    }

    public function setAttribute($key, $value)
    {
        log::debug('set attribute');
        log::debug('$key '. $key);
        if (in_array($key, $this->encryptdecrypttrait)) {
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    public function attributesToArray()
    {
        log::debug('get attribute to array');
        $attributes = parent::attributesToArray(); // call the parent method

        foreach (static::$encryptdecrypttrait as $key) {

            if (isset($attributes[$key])){
                $attributes[$key] = Crypt::decrypt($attributes[$key]);
            }
        }
        return $attributes;
    }

}