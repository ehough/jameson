jameson [![Build Status](https://secure.travis-ci.org/ehough/jameson.png)](http://travis-ci.org/ehough/jameson)
=====

JSON encoder/decoder compatible with PHP 5.1.3 and above. This library will use PHP's builtin
[`json_encode()`](http://php.net/json_encode) and [`json_decode()`](http://php.net/json_decode) when available.

    $input   = array('a' => 1, 'b' => 2, 'c' => 3);
    $encoder = new ehough_jameson_impl_FastEncoder();  //implements ehough_jameson_api_Encoder
    $decoder = new ehough_jameson_impl_FastDecoder();  //implements ehough_jameson_api_Decoder
    $asJson  = $encoder->encode($input);               //{"a":1,"b":2,"c":3}
    $result  = $decoder->decode($asJson);              //array('a' => 1, 'b' => 2, 'c' => 3);