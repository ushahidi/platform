# Image

Kohana 3.x provides a simple yet powerful image manipulation module. The [Image] module provides features that allows your application to resize images, crop, rotate, flip and many more.

## Drivers

[Image] module ships with [Image_GD] driver which requires `GD` extension enabled in your PHP installation. This is the default driver. Additional drivers can be created by extending the [Image] class.

## Getting Started

Before using the image module, we must enable it first on `APPPATH/bootstrap.php`:

~~~
Kohana::modules(array(
    ...
    'image' => MODPATH.'image',  // Image manipulation
    ...
));
~~~

Next: [Using the image module](using).