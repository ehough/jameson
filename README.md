## jameson

[![Build Status](https://secure.travis-ci.org/ehough/jameson.png)](http://travis-ci.org/ehough/jameson)
[![Project Status: Unsupported - The project has reached a stable, usable state but the author(s) have ceased all work on it. A new maintainer may be desired.](http://www.repostatus.org/badges/latest/unsupported.svg)](http://www.repostatus.org/#unsupported)
[![Latest Stable Version](https://poser.pugx.org/ehough/jameson/v/stable)](https://packagist.org/packages/ehough/jameson)
[![License](https://poser.pugx.org/ehough/jameson/license)](https://packagist.org/packages/ehough/jameson)

JSON encoder/decoder compatible with PHP 5.1.3 and above. This library will use PHP's builtin
[`json_encode()`](http://php.net/json_encode) and [`json_decode()`](http://php.net/json_decode) when available.

    $input   = array('a' => 1, 'b' => 2, 'c' => 3);
    $encoder = new ehough_jameson_impl_FastEncoder();  //implements ehough_jameson_api_Encoder
    $decoder = new ehough_jameson_impl_FastDecoder();  //implements ehough_jameson_api_Decoder
    $asJson  = $encoder->encode($input);               //{"a":1,"b":2,"c":3}
    $result  = $decoder->decode($asJson);              //array('a' => 1, 'b' => 2, 'c' => 3);
