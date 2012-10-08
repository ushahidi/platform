# Basic Usage

Shown here are the basic usage of this module. For full documentation about the image module usage, visit the [Image] api browser.

## Creating Instance

[Image::factory()] creates an instance of the image object and prepares it for manipulation. It accepts the `filename` as an arguement and an optional `driver` parameter. When `driver` is not specified, the default driver `GD` is used.

~~~
// Uses the image from upload directory
$img = Image::factory(DOCROOT.'uploads/sample-image.jpg');
~~~

Once an instance is created, you can now manipulate the image by using the following instance methods.

## Resize

Resize the image to the given size. Either the width or the height can be omitted and the image will be resized proportionally.

Using the image object above, we can resize our image to say 150x150 pixels with automatic scaling using the code below:

~~~
$img->resize(150, 150, Image::AUTO);
~~~

The parameters are `width`, `height` and `master` dimension respectively. With `AUTO` master dimension, the image is resized by either width or height depending on which is closer to the specified dimension.

Other examples:

~~~
// Resize to 200 pixels on the shortest side
$img->resize(200, 200);
 
// Resize to 200x200 pixels, keeping aspect ratio
$img->resize(200, 200, Image::INVERSE);
 
// Resize to 500 pixel width, keeping aspect ratio
$img->resize(500, NULL);
 
// Resize to 500 pixel height, keeping aspect ratio
$img->resize(NULL, 500);
 
// Resize to 200x500 pixels, ignoring aspect ratio
$img->resize(200, 500, Image::NONE);
~~~

## Render

You can render the image object directly to the browser using the [Image::render()] method.

~~~
$img = Image::factory(DOCROOT.'uploads/colorado-farm-1920x1200.jpg');

header('Content-Type: image/jpeg');

echo $img->resize(300, 300)
	->render();
~~~

What it did is resize a 1920x1200 wallpaper image into 300x300 proportionally and render it to the browser. If you are trying to render the image in a controller action, you can do instead:

~~~
$img = Image::factory(DOCROOT.'uploads/colorado-farm-1920x1200.jpg');

$this->response->headers('Content-Type', 'image/jpg');

$this->response->body(
	$img->resize(300, 300)
		->render()
);
~~~

[Image::render()] method also allows you to specify the type and quality of the rendered image.

~~~
// Render the image at 50% quality
$img->render(NULL, 50);
 
// Render the image as a PNG
$img->render('png');
~~~

## Save To File

[Image::save()] let's you save the image object to a file. It has two parameters: `filename` and `quality`. If `filename` is omitted, the original file used will be overwritten instead. The `quality` parameter is an integer from 1-100 which indicates the quality of image to save which defaults to 100.

On our example above, instead of rendering the file to the browser, you may want to save it somewhere instead. To do so, you may:

~~~
$img = Image::factory(DOCROOT.'uploads/colorado-farm-1920x1200.jpg');

$filename = DOCROOT.'uploads/img-'.uniqid().'.jpg';

$img->resize(300, 300)
	->save($filename, 80);
~~~

What we do is resize the image and save it to file reducing quality to 80% and save it to the upload directory using a unique filename.

## Other Methods

There are more methods available for the [Image] module which provides powerfull features that are best describe in the API documentation. Here are some of them:

* [Image::background()] - Set the background color of an image. 
* [Image::crop()] - Crop an image to the given size.
* [Image::flip()] - Flip the image along the horizontal or vertical axis.
* [Image::reflection()] - Add a reflection to an image.
* [Image::rotate()] - Rotate the image by a given amount.
* [Image::sharpen()] - Sharpen the image by a given amount.
* [Image::watermark()] - Add a watermark to an image with a specified opacity.

Next: [Examples](examples)