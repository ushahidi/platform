# Compass config file

add_import_path "media/bower_components/foundation/scss"

# Set this to the root of your project when deployed:
http_path = "/"
http_images_dir = "media/kohana/images"
css_dir = "media/css"
sass_dir = "media/scss"
images_dir = "media/images"
javascripts_dir = "media/js"
fonts_dir = "media/kohana/font"

# Production
output_style = :compressed

relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false
color_output = false

# enables Sass random() function
module Sass::Script::Functions
  def random(max = Sass::Script::Number.new(2000))
    Sass::Script::Number.new(rand(max.value), max.numerator_units, max.denominator_units)
  end
end
