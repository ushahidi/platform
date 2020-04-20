# Mixins

The Ushahidi Platform uses [Bourbon](http://bourbon.io/), a Sass mixin library. While Bourbon covers most of our mixin needs, there are times when custom mixins are needed. When that is the case each custom mixin is given it's own .scss file in the [mixins](https://github.com/ushahidi/platform-pattern-library/tree/gh-pages/assets/sass/utils/mixins) directory which are then @imported via the [\_mixins.scss](https://github.com/ushahidi/platform-pattern-library/blob/gh-pages/assets/sass/utils/_mixins.scss) file.

## Bourbon Mixins

* [Bourbon Docs](http://bourbon.io/docs/)

## Custom Mixins

Create a custom mixin when anticipating a reusable block of css.

### Example Mixin

```text
@mixin rotate($deg: 90deg) {
    -webkit-transform: rotate($deg);
    -moz-transform: rotate($deg);
    -ms-transform: rotate($deg);
    -o-transform: rotate($deg);
    transform: rotate($deg);
}
```

* The mixin title is _**rotate**_
* The default degree value is _**90 degrees**_

### How to Use

```text
.nav-icon {
    @include rotate(75deg);
}
```

* The mixin is called with _**@include**_
* _**75deg**_ overrides the 90deg default

