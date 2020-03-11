# Syntax and Formatting

The Ushahidi Platform uses the SCSS syntax because it is more consistent and familiar with vanilla CSS.

Indentation should be 4 space tabs. CSS rules should be written on multi-lines.

```text
// Yes
.foo {
    display: block;
    overflow: hidden;
    padding: 0 1em;
}

// No
.foo {
  display: block; overflow: hidden;

  padding: 0 1em;
}
```

White space after each declaration and after each ending curly brace.

```text
// Yes
p {
    font-size: em(16);

    @include media($small) {
        font-size: em(18);
    }

}

// No
p {
    font-size: em(16);
    @include media($small) {
        font-size: em(18);
    }
}
```

## Nesting

Nesting is one of the best features in Sass, but it can be overused and create unwanted CSS. A best practice is to nest no more than 3 levels deep and to only nest classes when that relationship is absolutely necessary to declare in order to achieve the desired style. Nesting for the sake of organizing the stylesheet is discouraged.

### Sass

```text
.unordered-list {
    list-style-type: none;

    .unordered-list__item {
        display: inline;

        a {
            color: blue;

            span {
                text-decoration: underline;
            }

        }

    }

}
```

### Compiled CSS Output

```text
.unordered-list {
    list-style-type: none;
}
.unordered-list .unordered-list__item {
    display: inline;
}
.unordered-list .unordered-list__item a {
    color: blue;
}
.unordered-list .unordered-list__item a span {
    text-decoration: underline;
}
```

## Color Formats

Hex is the default color format with RGBA being used when opacity is needed.

### Hex

Hex values should be written in all caps.

```text
// Yes
.foo {
  color: #FF0000;
}

// No
.foo {
  color: red;
}
```

### RGB

When using RGB, single space after commas and no spaces after parentheses.

```text
// Yes
.foo {
  color: rgba(0, 0, 0, 0.1);
}

// No
.foo {
  color: rgba( 0,0,0,0.1 );
}
```

## Numbers

Numbers should display leading zeros before a decimal value less than one and never display trailing zeros.

### Zeros:

```text
// Yes
.foo {
  padding: 2em;
  opacity: 0.5;
}

// No
.foo {
  padding: 2.0em;
  opacity: .5;
}
```

### Units:

```text
// Yes
$length: 2em;

// No
$length: 2;
```

### Calculations

```text
// Yes
.foo {
  width: (100% / 3);
}

// No
.foo {
  width: 100% / 3;
}
```

## Strings

Single quotes should be used with strings.

### Font Stack Example:

```text
// Yes
$font-stack: 'Helvetica Neue Light', 'Helvetica', 'Arial', sans-serif;

// No
$font-stack: "Helvetica Neue Light", "Helvetica", "Arial", sans-serif;

// No
$font-stack: Helvetica Neue Light, Helvetica, Arial, sans-serif;
```

### Image URL Example:

```text
// Yes
.foo {
  background-image: url('/images/kittens.jpg');
}

// No
.foo {
  background-image: url(/images/kittens.jpg);
}
```

## Commenting

Commenting is CSS is an essential practice that can help explain why and how code is written. Ushahidi's CSS commenting is simple.

```text
// Comments are preceeded by two backslashes
.foo {
    background: #000;
}
```

## Css Rulesets

* related selectors on the same line; unrelated selectors on new lines;
* the opening brace \({\) spaced from the last selector by a single space;
* each declaration on its own new line;
* a space after the colon \(:\);
* a trailing semi-colon \(;\) at the end of all declarations;
* the closing brace \(}\) on its own new line;
* a new line after the closing brace \(}\).

```text
// Yes
.foo, .foo-bar,
.baz {
  display: block;
  overflow: hidden;
  margin: 0 auto;
}

// No
.foo,
.foo-bar, .baz {
    display: block;
    overflow: hidden;
    margin: 0 auto }
```

## Letter Case Usage & Naming Conventions

When naming classes use lower case letters and a hypen between words.

```text
// Yes
.button-primary

//No
.ButtonPrimary

//No
.button_primary
```

