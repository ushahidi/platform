# Icons

The Platform Pattern Library icons are part of the ["Open Iconic"](https://useiconic.com/open) library, and are served up by an [SVG sprite](https://github.com/ushahidi/platform-pattern-library/blob/master/assets/img/iconic-sprite.svg). This allows us to display all icons in the set with a single request. A list of available icons can be found [here](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/1_basics/#pl-pattern-icons).

## HTML

Use [this HTML block](https://github.com/ushahidi/platform-pattern-library/blob/master/pattern-library/partials/_iconic-sample.html#L5-L7) wherever an icon is needed and adjust the [image path and \#icon-name](https://github.com/ushahidi/platform-pattern-library/blob/master/pattern-library/partials/_iconic-sample.html#L6)accordingly. Icon names can be found [here](http://preview.ushahidi.com/platform-pattern-library/master/assets/html/1_basics/#pl-pattern-icons).

## CSS

Icons can be styled via the svg.iconic class.

```text
svg.iconic {
    fill: red;
    width: 24px
    height: 24px
    etc...
}
```

