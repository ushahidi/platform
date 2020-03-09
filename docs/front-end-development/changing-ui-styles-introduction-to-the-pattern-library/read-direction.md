# Read Direction

Ushahidi's user interface displays LTR \(left-to-right\) by default, but Ushahidi also supports RTL \(right-to-left\), so if the user's preferred language is a RTL language, the UI will display accordingly.

When building this feature, it was important to maintain one code base that supported both LTR and RTL read direction. In order to accomplish this we decided to use a library of Sass mixins and functions to automatically flip the CSS styles for RTL \(right-to-left\) read direction when needed. We decided to go with [RTL-Sass](https://github.com/jamesl1001/RTL-Sass) open-source library, which supports the following properties:

* background
* background-position
* border
* border-radius
* clear
* cursor
* direction
* float
* left/right
* margin
* padding
* text-align
* text-indent

### LTR/RTL body class

**ltr-namespace** is the default body class, but if a user selects an RTL language when creating a deployment, that body class will change to **rtl-namespace** and display all UI elements as RTL.

### Conflicts

The Ushahidi UI uses the lightweight semantic grid [Bourbon Neat](http://neat.bourbon.io/) and this Neat span-columns mixin:

```text
@include span-columns(12);
```

which compiles to:

```text
float: left;
display: block;
width: 100%;
```

but in order to correctly display the float in both LTR _and_ RTL, you have to manually override the float: left; like so:

```text
@include span-columns(12);
@include float(left);
```

which compiles to:

```text
float: left; // would be overridden
display: block;
width: 100%;
float: left; // would be left or right depending on language
```

## Supported Properties

### Example

```text
@include border-left(3px solid #000);
```

would compile to:

```text
border-left: 3px solid #000 // default LTR

border-right: 3px solid #000 // RTL
```

### Background & Background Position

```text
#background-position {
    @include background-position(8px 100px);
}
#background-position-left {
    @include background-position(left 100px);
}
#background-position-right {
    @include background-position(right 100px);
}
#background-position-center {
    @include background-position(center 100px);
}
#background-1 {
    @include background(url(../img/ushahidi-logo-black.svg) no-repeat 20px center);
}
#background-1-left {
    @include background(url(../img/ushahidi-logo-black.svg) no-repeat left center);
}
#background-1-right {
    @include background(url(../img/ushahidi-logo-black.svg) no-repeat right center);
}
#background-1-center {
    @include background(url(../img/ushahidi-logo-black.svg) no-repeat center center);
}
#background-2 {
    @include background(#999 url(../img/ushahidi-logo.svg) no-repeat 8px center);
}
#background-2-left {
    @include background(#999 url(../img/ushahidi-logo.svg) no-repeat left center);
}
#background-2-right {
    @include background(#999 url(../img/ushahidi-logo.svg) no-repeat right center);
}
#background-2-center {
    @include background(#999 url(../img/ushahidi-logo.svg) no-repeat center center);
}
```

### Border

```text
#border-left {
    @include border-left(3px solid #000);
}
#border-right {
    @include border-right(3px solid #000);
}
```

### Border Radius

```text
#border-top-left-radius {
    @include border-top-left-radius(10px);
}
#border-top-right-radius {
    @include border-top-right-radius(10px);
}
#border-bottom-left-radius {
    @include border-bottom-left-radius(10px);
}
#border-bottom-right-radius {
    @include border-bottom-right-radius(10px);
}
```

### Clear

```text
#clear-origin-left {
    @include float(left);
    @include clear(left);
}
#clear-origin-right {
    @include float(right);
    @include clear(right);
}
```

### Cursor

```text
#cursor-e {
    @include cursor(e-resize);
}
#cursor-ne {
    @include cursor(ne-resize);
}
#cursor-se {
    @include cursor(se-resize);
}
#cursor-w {
    @include cursor(w-resize);
}
#cursor-nw {
    @include cursor(nw-resize);
}
#cursor-sw {
    @include cursor(sw-resize);
}
```

### Direction

```text
#direction {
    @include direction;
}
```

### Float

```text
#float-origin-left {
    @include float(left);
}
#float-origin-right {
    @include float(right);
}
```

### Left/Right

```text
#left {
    @include left(20px);
}
#right {
    @include right(20px);
}
```

### Margin

```text
#margin-0-8px-16px-24px {
    @include margin(0 8px 16px 24px);
}
#margin-left {
    @include margin-left(20px);
}
#margin-right {
    @include margin-right(20px);
}
```

### Padding

```text
#padding-0-8px-16px-24px {
    @include lrswap(padding, 0 8px 16px 24px);
}
#padding-left {
    @include padding-left(20px);
}
#padding-right {
    @include padding-right(20px);
}
```

### Text Alignment & Indent

```text
#text-align-left {
    @include text-align(left);
}
#text-align-right {
    @include text-align(right);
}
#text-indent {
    @include text-indent(20px);

    text-align: left;
}
```

