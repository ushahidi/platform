# Variables

Variables are defined in [\_settings.scss](https://github.com/ushahidi/platform-pattern-library/blob/gh-pages/assets/sass/_settings.scss). A few common variables are breakpoints, color, z-index and spacing, but other variables are available.

## Example use

When using a color more than once, store it in a variable with a meaningful name representing the color.

```text
// The variable is set within the .setting.scss file then used 
//across the .scss files.
// This allows you to update the whole code base just by editing 
//one instance, in this example $blue.

$blue: #2274b4;

.class {
    color: $blue;
}

.class-2 {
    border-color: $blue;
}

.class- {
    background-color: $blue;
}
```

