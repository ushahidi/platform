# Grid, Breakpoints, & Media Queries

The Ushahidi Platform grid system is built with [Neat](http://neat.bourbon.io/) by [Thoughtbot](https://thoughtbot.com/). Neat is a lightweight semantic grid framework for Sass and Bourbon.

## Links

* [Neat Examples](http://neat.bourbon.io/examples/)
* [Neat Docs](http://thoughtbot.github.io/neat-docs/latest/)

The Ushahidi Platform uses a mobile-first design approach, meaning we design with smaller devices as our default, then add components and adjust layouts as needed when the screen size increases. This approach allows the app to function and convey the appropriate information regardless of the user's device. It works on mobile phones and desktop computers alike.

## Breakpoints

The breakpoints are defined under _Grid_ within [\_settings.scss](https://github.com/ushahidi/platform-pattern-library/blob/gh-pages/assets/sass/_settings.scss)

```text
/*------------------------------------*\
    $GRID
\*------------------------------------*/

$grid-columns: 12;     # The number of columns in the grid
$gutter: 1.6888em;     # The width of margin between columns
$max-width: 1580px;    # The max width of the grid

// Breakpoint Widths
$mobile-min-width: (30em) !default; // 480px
$mobile-up-min-width: (30.063em) !default; // 481px
$small-min-width: (48em) !default; // 768px
$medium-min-width: (64em) !default; // 1024px
$large-min-width: (80em) !default; // 1280px
$xlarge-min-width: (90em) !default; // 1440px
$xxlarge-min-width: (120em) !default; // 1920px
$tall-min-height: (650px) !default; // for vertical breakpoints
$xtall-min-height: (850px) !default; // for vertical breakpoints -- taller

// Defined Breakpoints
// thoughtbot.github.io/neat-docs/latest/#new-breakpoint
$mobile: new-breakpoint(min-width $mobile-min-width);
$mobile-up: new-breakpoint(min-width $mobile-up-min-width);
$small: new-breakpoint(min-width $small-min-width);
$medium: new-breakpoint(min-width $medium-min-width);
$large: new-breakpoint(min-width $large-min-width);
$xlarge: new-breakpoint(min-width $xlarge-min-width);
$xxlarge: new-breakpoint(min-width $xxlarge-min-width);
$tall: new-breakpoint(min-width $small-min-width min-height $tall-min-height);
$xtall: new-breakpoint(min-height $xtall-min-height);
```

## How to Use

You can call a breakpoint with this _@include_ syntax.

```text
.class {
    // default mobile first styles

    @include media($small) {
        // $small styles - 768px and up
    }

    @include media($medium) {
        // $medium styles - 1024px and up
    }

    etc... {

    }

}
```

