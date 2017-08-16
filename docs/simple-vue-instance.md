# Creating a simple Vue Instance

Assuming you've already installed via composer, and added the ServiceProvider...

## Vue Setup

Assuming you've installed Vue & Vuex with npm, and are using Laravel Mix... You should be able to set up your vuejs app like so.

```js
window.Vue = require('vue');
window.Vuex = require('vuex');

const store = new Vuex.Store({
	state: {
		username: null,
	},
});

const App = new Vue({
	name: 'App',
	el: '#app',
	template: '#app-template',
	store,
});
```

This will create a basic state, where we're expecting a username variable to be set. However, we're missing the `#app-template` template, as well as the intial data to be populated in username.

Assuming this file is built to `/public/app.js` add it to your template with

```blade
<script type='text/javascript' src="{{ mix('/js/app.js') }}"></script>
```


## Creating our template.

We need to create a VueComponent template with the id `app-template` to be picked up by the js app. Our goal is to make a single blade file, that will render to both a JS Template, and staticly in the view.

Creating `resources/views/app.blade.php` to be used as our component file.

> When we include our component with `@vueComponent`, it is passed `$vue` as a boolean, depending on if its meant to be php, or a vue js template.

```blade
<div id='app' {!! $vue ? '' : 'server-rendered="true"' !!}>
	hello.
</div>
```

Here we've done two things.

1. *We created a single wrapping element with our matching element ID.* This will sync our dom w/ our javascript vue app above.

2. For our main vue element, we also set *server rendered true* on the javascript instance. This will [Allow vue to hydrate, not replace the existing dom](https://github.com/vuejs/vue-ssr-docs/blob/master/en/hydration.md)

Now, to include this template in our view.

```blade
@vueComponent(app)
```

This will render our div in the source (however if your Vue instance is running, it will remove the dom since we're still missing the JS Template) 

```html
<div id='app' server-rendered="true">
	hello.
</div>
```

## Including the JS Template

When you use `@vueComponent(app)` it will render the PHP inline. As well, it will push the js template to a stack called `vue`. You can add the following to your blade template, and it will render out the scripts pushed to the that stack.

```blade
@stack('vue')
```

After you've also rendered out the vue template, you should see `hello` rendered in your view. If you view source, you should see both the static rendered component from the previous step, and this JS Template.

```html
<script type="text/x-template" id="app-template"><div id='app' >
	hello.
</div>
</script>
```

> The `@vueComponent(app)` command automatically wrapped the element with `id='{component-name}-app'`



---

At this point, with the above written...

Your vue instance should init, and mount over the div with hello. No VueJS Errors.

---

## Rendering a variable from the server in VueX

We need to pass a state object to our vue. This `$initialState` will include the items to be expected in the Vuex State, in the same format. When this is passed to the view, it will replace the default vuex state.

Lets set up an example object, returned to our view like...

```php
Route::get('/', function () {
    // matches our VueX State, preventing jank when Vue Mounts
    $initial_state = [
        'username' => 'Cool Guy From PHP',
    ];

    return view('welcome')
        ->with('initial_state', $initial_state);
});
```
This provides the necessary data to the view. We can now use `@vue()` to echo a variable to our php/js rendered components. We can update `app.blade.php` to:

```blade
<div id='app' {!! $vue ? '' : 'server-rendered="true"' !!}>
	hello, my name is @vue('$store.state.username', $initial_state['username'])
</div>
```

*In our JS Template*, we will echo `{{ $store.state.username }}` for the vue instance.

*In our PHP Template*, we will echo `Cool Guy From PHP` directly into the source.

You should now see your rendered source like:

```html
<div id='app' server-rendered="true">
	hello, my name is Cool Guy From PHP
</div>

<script type="text/x-template" id="app-template"><div id='app' >
	hello, my name is {{$store.state.username}}
</div>
```

When looking at the rendered version however, you will see `hello, my name is`.

This is because *our Vuex instance doesn't have username set to match our server rendered version.* To fix this, we need to replace our state.

Lets update our js include to be:

```blade
<script>window.__INITIAL_STATE__='{!! json_encode($initial_state) !!}'</script>
<script type='text/javascript' src="{{ mix('/js/simple.js') }}"></script>
```

Now using that global `__INITIAL_STATE__` var, we can replace our vuex state. Lets update our app.js to:

```js
window.Vue = require('vue');
window.Vuex = require('vuex');

const store = new Vuex.Store({
	state: {
		username: null,
	},
});

// the magic, where we update our state.
if (window.__INITIAL_STATE__) {
	store.replaceState(JSON.parse(window.__INITIAL_STATE__));
}

const testApp = new Vue({
	name: 'TestAppRoot',
	el: '#app',
	template: '#app-template',
	store,
});

```

Now that our Vuex state, will match our php rendered var, you should see `hello, my name is Cool Guy From PHP`


---

At this point, you've now created a super simple vue app, that is rendered statically in PHP, as well as mounted jank-free into Vue & Vuex.

At this point, your app is reactive and can be updated through user interaction like normal. For example...

---

## A super basic 'updating variable' example.

Lets go back to our test app instance, and add an update on `created()`, and change the variable via VueX.

```js
window.Vue = require('vue');
window.Vuex = require('vuex');

const store = new Vuex.Store({
	state: {
		username: null,
	},
	mutations: {
		changeUsername(state) {
			state.username = 'Really, Really Cool Guy From JS';
		},
	},
});

// PHP has set __INITIAL_STATE__ to match our vuex store / data rendered live.
if (window.__INITIAL_STATE__) {
	store.replaceState(JSON.parse(window.__INITIAL_STATE__));
}

const testApp = new Vue({
	name: 'TestAppRoot',
	el: '#app',
	template: '#app-template',
	store,
	created() {
		setTimeout(() => {
			store.commit('changeUsername');
		}, 2000);
	},
});
```
With this example, you should now see `hello, my name is Cool Guy From PHP`

And after 2 seconds it updates to `hello, my name is Really, Really Cool Guy From JS`
















