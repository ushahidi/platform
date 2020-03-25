# Helpers

The assets/sass/utils/\_helpers.scss file provides a handful of helper classes.

## Example

The .divider helper class provides a quick way to display a page/section divider.

### Sass

```text
.divider  {
    height: 1px;
    width: 100%;
    background: $lt-gray;
    margin: $sm-spacing 0;
    clear: both;

    &.padded {
        margin: $base-spacing 0;
    }

    &.white {
        background: $white;
    }
}
```

### In HTML

class="divider"\(default\)

class="divider padded"\(with padding\)

class="divider white"\(to be used over a dark background\)

