<?php

function benc_resp($d)
{
    benc_resp_raw(benc(array('type' => 'dictionary', 'value' => $d)));
}

function benc_resp_raw($x)
{
    header("Content-Type: text/plain");
    header("Pragma: no-cache");

    if ($_SERVER['HTTP_ACCEPT_ENCODING'] == 'gzip') {
        header("Content-Encoding: gzip");
        echo gzencode($x, 9, FORCE_GZIP);
    } else
        echo $x;
}

function benc($obj)
{
    if (!is_array($obj) || !isset($obj["type"]) || !isset($obj["value"]))
        return;
    $c = $obj["value"];
    switch ($obj["type"]) {
        case "string":
            return benc_str($c);
        case "integer":
            return benc_int($c);
        case "list":
            return benc_list($c);
        case "dictionary":
            return benc_dict($c);
        default:
            return;
    }
}

function benc_str($s)
{
    return strlen($s) . ":$s";
}

function benc_int($i)
{
    return "i" . $i . "e";
}

function benc_list($a)
{
    $s = "l";
    foreach ($a as $e) {
        $s .= benc($e);
    }
    $s .= "e";
    return $s;
}

function benc_dict($d)
{
    $s = "d";
    $keys = array_keys($d);
    sort($keys);
    foreach ($keys as $k) {
        $v = $d[$k];
        $s .= benc_str($k);
        $s .= benc($v);
    }
    $s .= "e";
    return $s;
}


function hex2bin($hex)
{
    $r = '';
    for ($i = 0; $i < strlen($hex); $i += 2) {
        $r .= chr(hexdec($hex{$i} . $hex{($i + 1)}));
    }
    return $r;
}
