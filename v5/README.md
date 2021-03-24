# Keys "values" of the rewrite
Taking some notes of how this is done, so we continue adding and thinking about is as we progress.
It's also useful for code reviews so we can be called out when we (probably by mistake) act against these definitions :) 

## Safe defaults
- The default behavior should be the safer one. Prefer `false` over `true` returns when authorizing, prefer not needing a special scope vs needing it when queries are run. 
- Why? because it's harder to mess it up. You won't have to worry about someone forgetting a scope for instance, because without it, the default will be safe

### Examples

Auth checks

#### Wrong

```
function sillyAuth() {
    if (!$authorized) {
        return false;
    }
    return true;
}
```

#### Better

```
function sillyAuth() {
    if ($authorized) {
        return true;
    }
    return false;
}

```

---

Database queries

#### Wrong

```
public function show($id) {
    User::allowed()->find($id);
}

//checks if the value can be retrieved when you call "::allowed" in your model
public function scopeAllowed() {
...
}
// when you don't want to check you just do
User::find($id)
```

#### Better

```
public function show($id) {
    User::find($id);
}

// adds a global scope that checks if user can get the values
protected static function boot()
{
    parent::boot();
    /**
     * This is cool because we don't have to worry about calling ::allowed
     * each time to be safe that we are only getting authorized data. It's saving us
     * from ourselves :)
     */
    static::addGlobalScope(new CategoryAllowed);
}
// when you don't want to check it's explicit, you do: 
Category::withoutGlobalScopes()->find($id);

```

## Prefer simplicity.

Do the simple, functional thing first.
I promise there is always time to add more code, more classes, more interfaces. Start simple and build it up.

Some examples: 
- If you don't need a generic validation class, don't make one. You can extract the generics later when you add more models.
- When you notice a few things are repeating the same behavior, go ahead and normalize it. If more than one person worked on those, involve them in the discussion and refactor together.
- If something is hard to read, write the tests that validate it works, then refactor into a more readable version. We prioritize shared understanding over fancyness, and we prioritize reading code because it's the most common action taken on any given codebase.
- If your method has more than 5 parameters, you should ask yourself why. Are we missing a class, maybe? Is the method doing too much? What's going on here?

