# File-structure

## File Structure

The Ushahidi Platform front-end is served via the [assets folder](https://github.com/ushahidi/platform-pattern-library/tree/master/assets). Production files are compiled via [gulpfile.js](https://github.com/ushahidi/platform-pattern-library/blob/master/gulpfile.js).

### HTML/Handlebars

HTML templates are compiled via Handlebars. A .html template must be created in the [pattern-library/\_layouts](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/5_layouts) directory, then a .hbs template must be created in the [assets/templates](https://github.com/ushahidi/platform-pattern-library/tree/master/assets/templates) directory. Handlebars will then compile the production html into the [assets/html](https://github.com/ushahidi/platform-pattern-library/tree/master/assets/html) directory.

{% hint style="danger" %}
Warning: Assets/html displaying "page not found" error
{% endhint %}

### Sass/CSS

Each UI pattern gets it's own .scss file, then each of those files are organized by their respective sections \(Basics/Fragments/Modules etc...\) and @imported within the [style.scss](https://github.com/ushahidi/platform-pattern-library/blob/master/assets/sass/style.scss) file.

The CSS is then compiled from [assets/sass/style.scss](https://github.com/ushahidi/platform-pattern-library/blob/master/assets/sass/style.scss) to assets/css/style.css.

### Javascript

Each Javascript pattern gets it's own .js file within the [custom](https://github.com/ushahidi/platform-pattern-library/tree/master/assets/js/custom) directory.

The Javascript is then compiled from [assets/js/custom](https://github.com/ushahidi/platform-pattern-library/tree/master/assets/js/custom) to assets/js/app.js.

PL specfic javascript follows the same pattern but originates from [assets/js/pattern-library](https://github.com/ushahidi/platform-pattern-library/tree/master/assets/js/pattern-library)

### Structure of templates

#### [Basics](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/1_basics)

Basics are the components that make up fragments. Oftentimes, a basic can't even stand on its own as a UI element \(for example, a color\) but it is required to create other things. All basics are just one html element.

#### [Fragments](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/2_fragments)

Fragments are made up of more than one html element, but usually do not stand on their own. Fragments are combined to create modules.

#### [Modules](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/3_modules)

Modules are standalone UI blocks, each of them complete and serving a unique purpose. Multiple modules can create a block.

#### [Blocks](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/4_blocks)

Blocks are full components that can be combined to create layouts. While a block can contain multiple modules, including repeating modules, a block is never repeated in a layout.

#### [Layouts](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/5_layouts)

Layouts are the structures in which blocks are placed to create pages. Each layout can be reused to create unique pages, depending on what blocks are placed in them.

#### [Partials](https://github.com/ushahidi/platform-pattern-library/tree/master/pattern-library/partials)

Partials are files with blocks of code that appear in more than one place throughout the Pattern Library.

