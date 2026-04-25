<?php

$driver = strtolower((string) env('IMAGE_DRIVER', 'gd'));

$drivers = [
    'gd' => 'Intervention\\Image\\Drivers\\Gd\\Driver',
    'imagick' => 'Intervention\\Image\\Drivers\\Imagick\\Driver',
];

$vipsDriver = 'Intervention\\Image\\Drivers\\Vips\\Driver';
if (class_exists($vipsDriver)) {
    $drivers['vips'] = $vipsDriver;
}

return [
    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    | Intervention Image supports "gd" and "imagick" out of the box.
    | The "vips" driver is supported when intervention/image-driver-vips
    | is installed.
    | The IMAGE_DRIVER value is normalized to the corresponding driver class.
    | If an unknown value is provided, it falls back to the GD driver.
    */
    'driver' => $drivers[$driver] ?? $drivers['gd'],

    /*
    |--------------------------------------------------------------------------
    | Driver Options
    |--------------------------------------------------------------------------
    | These options are passed to the image manager driver.
    */
    'options' => [
        'autoOrientation' => true,
        'decodeAnimation' => true,
        'blendingColor' => 'ffffff',
        'strip' => false,
    ],
];
