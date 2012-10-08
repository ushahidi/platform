# Crop Profile Image

This example is very similar to our previous example and even uses the same upload logics. The only difference is that the uploaded image is cropped to square from the center whose dimension is half the original height of the image. 

## Controller

We name our new controller as `Controller_Crop` and accessible through `/crop` URL. Assuming that your project is located at [http://localhost/kohana](http://localhost/kohana), then our crop controller is at [http://localhost/kohana/crop](http://localhost/kohana/crop).

~~~
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Crop extends Controller {

	public function action_index()
	{
		$view = View::factory('crop/index');
		$this->response->body($view);
	}
	
	public function action_do()
	{
		$view = View::factory('crop/do');
		$error_message = NULL;
		$filename = NULL;
		
		if ($this->request->method() == Request::POST)
		{
			if (isset($_FILES['avatar']))
			{
				$filename = $this->_save_image($_FILES['avatar']);
			}
		}
		
		if ( ! $filename)
		{
			$error_message = 'There was a problem while uploading the image.
				Make sure it is uploaded and must be JPG/PNG/GIF file.';
		}
		
		$view->uploaded_file = $filename;
		$view->error_message = $error_message;
		$this->response->body($view);
	}
	
	protected function _save_image($image)
	{
		if (
			! Upload::valid($image) OR
			! Upload::not_empty($image) OR
			! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
		{
			return FALSE;
		}
		
		$directory = DOCROOT.'uploads/';
		
		if ($file = Upload::save($image, NULL, $directory))
		{
			$filename = strtolower(Text::random('alnum', 20)).'.jpg';
			
			$img = Image::factory($file);
			
			// Crop the image square half the height and crop from center
			$new_height = (int) $img->height / 2;
			
			$img->crop($new_height, $new_height)
				->save($directory.$filename);
				
			// Delete the temporary file
			unlink($file);
			
			return $filename;
		}
		
		return FALSE;
	}
	
}
~~~

The `index` action displays the upload form whereas the `do` action will process the uploaded image and provides feedback to the user.

In `do` action, it checks if the request method was `POST`, then delegates the process to `_save_image()` method which in turn performs various checks and finally crops and saves the image to the `uploads` directory.

## Views

For the upload form (the `index` action), the view is located at `views/crop/index.php`.

~~~
<html>
	<head>
		<title>Upload Profile Image</title>
	</head>
	<body>
		<h1>Upload your profile image</h1>
		<form id="upload-form" action="<?php echo URL::site('crop/do') ?>" method="post" enctype="multipart/form-data">
			<p>Choose file:</p>
			<p><input type="file" name="avatar" id="avatar" /></p>
			<p><input type="submit" name="submit" id="submit" value="Upload and crop" /></p>
		</form>
	</body>
</html>
~~~

View for `crop/do` action goes to `views/crop/do.php`.

~~~
<html>
	<head>
		<title>Upload Profile Image Result</title>
	</head>
	<body>
		<?php if ($uploaded_file): ?>
		<h1>Upload success</h1>
		<p>
			Here is your uploaded and cropped avatar:
			<img src="<?php echo URL::site("/uploads/$uploaded_file") ?>" alt="Uploaded avatar" />
		</p>
		<?php else: ?>
		<h1>Something went wrong with the upload</h1>
		<p><?php echo $error_message ?></p>
		<?php endif ?>
	</body>
</html>
~~~

## Screenshots

Below are screenshots for this example.

![Original image](crop_orig.jpg)

_Original image to upload_

![Upload image form](crop_form.jpg)

_Upload image form_

![Upload result page](crop_result.jpg)

_Upload result form_