# Create a New Component from Scratch

Follow these steps when creating a new UI component.

## Where does the UI fit within the Pattern Library?

The Platform Pattern Library is modeled after Brad Frost's [Atomic Design](http://atomicdesign.bradfrost.com/), where smaller UI elements make up bigger elements which are then combined to create full pages. In [Atomic Design](http://atomicdesign.bradfrost.com/) **Atoms** are used to create **Molecules**, which create **Organisms**, which create **Templates**, all of which come together to create **Pages**. In the Pattern Library we follow the same methodology but with **Basics**, **Fragments**, **Modules**, and **Blocks** being used to create **Layouts**.

So the first thing that needs to be determined is where the new UI fits withing this methodology. Is it a small compenent? Or is it a bigger component made up of other smaller components? Once the appropriate structure is determined, you will be able to create the HTML and Sass accordingly.

## HTML

Each UI component exists in either [**Basics**](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/1_basics/), [**Fragments**](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/2_fragments/), [**Modules**](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/3_modules/), or [**Blocks**](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/4_blocks/) with each having their own [index.html file](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/1_basics) _\(links to basic's index.html\)_.

### Creating the HTML for a new component

* add the new html component to the appropriate file above.
* add a link in that page's nav and link it to the new html component on that page.
* use that page's existing html and nav as a guide.

### Creating a new layout template.

[**Layouts**](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/5_layouts/) are compiled via [Handlebars](https://handlebarsjs.com/).

* create a new file [here](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/5_layouts). _\(use existing html files as a guide\)_
* create a handlebars file [here](https://github.com/ushahidi/platform-pattern-library/tree/master/assets/templates). 
* add the appropriate html for the layout to the new .hbs file.
* link to the new layout .html file [here](https://github.com/ushahidi/platform-pattern-library/blob/master/pattern-library/5_layouts/index.html).

## Sass

The Platform Pattern Library uses [Sass](https://sass-lang.com/) to compile Css.

### Styling the new component

Sass files are structured using the same methodology as above.

* create a new .scss file in the[ Sass directory](https://github.com/ushahidi/platform-pattern-library/blob/master/assets/sass).
* @import that file to [/assets/sass/**style.scss**](https://github.com/ushahidi/platform-pattern-library/blob/master/assets/sass/style.scss)\*\*\*\*

