<?php
namespace LootMarket\VueComponent;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class VueComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * vueComponent Blade Directive
         * Will include the PHP output of a BladeVue Component, and push_once the JS template
         * for a Bladevue Component
         *
         * @string $componentName Name of the vue Component/Blade File to be rendered
         * @string $path can optionally specify a path to the component
         */
        Blade::directive('vueComponent', function ($expression) {
            // Check if our expression includes a path, and extract it if so
            if (strpos($expression, ', ') !== false) {
                list($componentName, $path) = explode(', ', $expression);
            } else {
                $componentName = $expression;
                $path = '';
            }
            

            $pushKey = '__pushonce_vue_'.str_slug($componentName);

            // pushes (only once!) the vuejs version of this to vue stack
            // makes a view, with the existing view data passed in, and renders it.
            return "<?php
                    if(! isset(\$__env->$pushKey)): \$__env->$pushKey = 1; \$__env->startPush('vue');
                    echo '<script type=\"text/x-template\" id=\"{$componentName}-template\">';
                    echo \$__env->make('{$path}/{$componentName}', array_except(get_defined_vars(), ['__data', '__path']))->with(['vue' => true]);
                    echo '</script>';
                    \$__env->stopPush(); endif;

                    echo \$__env->make('{$path}/{$componentName}', array_except(get_defined_vars(), ['__data', '__path']))->with(['vue' => false])->render();
                ?>";
        });


        /**
         * vue Blade Directive
         * Will include a different variable type based on being vue or not.
         * Expects $vue to be a boolean
         */
        Blade::directive('vue', function ($expression) {
            list($vueVariable, $phpVariable) = explode(', ', $expression);
            return "{{ \$vue ? @v($vueVariable) : $phpVariable }}";
        });

        /**
         * v(ariable) Blade Directive
         * Takes a 'string' and returns it as {{ string }} for use in vue templates.
         * Called by @vue() directive, but can be used standalone as @v()
         */
        Blade::directive('v', function($expression) {
            return "<?php echo '{{'.$expression.'}}'; ?>";
        });

    }
}
