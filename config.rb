# Compass config file
require "sass-css-importer"
add_import_path "web/media/bower_components/foundation/scss"

# Set this to the root of your project when deployed:
http_path = "/"
http_images_dir = "media/images"
css_dir = "web/media/css"
sass_dir = "web/media/scss"
images_dir = "web/media/images"
javascripts_dir = "web/media/js"
fonts_dir = "web/media/font"

# Production
output_style = :compressed

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
