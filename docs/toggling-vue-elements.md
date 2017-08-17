# Toggling an Element with v-if && v-show

The goal here is to toggle an element in php, as well as in vue using a `@vueComponent()`

There are two types of 'element toggles' in Vue. `v-if` and `v-show`.

1. `v-if` will remove/add the element from the dom.

2. `v-show` will use css to toggle the elements state.

> With laravel-vue-blade-components `v-show` is much easier/cleaner to handle than a `v-if` at this time.



## Toggling an element with `v-if`

The vue part is quite simple, in our component:

```blade
@if ($vue)
	<span v-if='user.admin'>User is an admin!</span>
	<span v-else>User is NOT an admin!</span>
@endif
```

In our vue template, this will now swap the span in the dom when set up. We however need to make this do the same for PHP when `$vue` is false.

To do so, we need to use an elseif & check the same variable in PHP. using the above example of user.admin...


```blade
@if ($vue)
	<span v-if='user.admin'>User is an admin!</span>
	<span v-else>User is NOT an admin!</span>
@elseif (!$vue && $initialState['user']['admin'])
	<span>User is an admin!</span>
@else
	<span>User is NOT an admin!</span>
@endif
```

Doing this, will result in:

1. Only one span initially rendered by PHP.
2. VueJS will happily mount onto this, as `v-if` would render only one span.
3. Template resumes as normaly, quietly hydrating.


## Toggling an element with `v-show`

Since v-show is based on css, we need to toggle the element using `style="display:none;"` instead of preventing the element from being rendered.

Using the same user is admin example, we can accomplish this like so:

```blade
<span v-show='user.admin' {{ $vue && $initialState['user']['admin'] ? '' : 'style="display:none;"' }}>User is an admin!</span>
<span v-show='! user.admin' {{ $vue && $initialState['user']['admin'] ? 'style="display:none;"' : '' }}>User is NOT an admin!</span>
```

Doing this, will result in:

1. Only one span initially shown, thanks to `style="display:none;"`
2. VueJS will happily mount onto this, since elements will match in both php && js templates.
3. Template resumes as normaly, quietly hydrating.
