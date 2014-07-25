# Compass config file
require "compass-csslint"
require "sass-css-importer"
add_import_path "media/bower_components/foundation/scss"

# Set this to the root of your project when deployed:
http_path = "/"
http_images_dir = "media/images"
css_dir = "media/css/test"
sass_dir = "media/scss"
images_dir = "media/images"
javascripts_dir = "media/js"
fonts_dir = "media/font"

# Development
output_style = :expanded

relative_assets = true
sourcemap = true
color_output = false
disable_warnings = true
# To disable debugging comments that display the original location of your selectors. Uncomment:
# line_comments = false

# enables Sass random() function
module Sass::Script::Functions
  def random(max = Sass::Script::Number.new(2000))
    Sass::Script::Number.new(rand(max.value), max.numerator_units, max.denominator_units)
  end
end
