<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Used to generate a random id for reference later
        $currentScriptId = 0;

        Blade::directive('scope', function () use (&$currentScriptId) {
            $currentScriptId++;

            return <<<SCRIPT_ECHO
            <div>
                <?php
                \$tagConfigs = [
                    //[
                    //   'tag' => 'noscript'
                    //],
                    [
                        'tag' => 'template',
                        'attributes' => [
                            'style' => 'display:none',
                            'id' => 'scoped-element-$currentScriptId',
                        ]
                    ],
                ];
                foreach (\$tagConfigs as \$k => \$tagConfig) {
                    extract(\$tagConfig);

                    echo "<\$tag";

                    if(isset(\$attributes)) {
                        foreach (\$attributes as \$attribute => \$value) {
                            echo " \$attribute=\"\$value\"";
                        }
                    }

                    echo ">";
                ?>
            SCRIPT_ECHO;
        });
        Blade::directive('endscope', function () use (&$currentScriptId) {
            return <<<SCRIPT_ECHO
                <?php
                    echo "</\$tag>";
                } ?>

                <script data-re-execute-on-livewire-update>
                    (function(cacheBreaker) {
                        if(window.__isReExecutingScript){ // prevents recursion
                            window.__isReExecutingScript = false;
                            return;
                        }

                        const templateEl = document.getElementById('scoped-element-$currentScriptId');
                        const parentEl = templateEl.parentNode;
                        const content = templateEl.content.cloneNode(true);

                        const shadow = parentEl.shadowRoot || parentEl.attachShadow({ mode:"open" });

                        // Smart replace only the changed nodes
                        var childNodes = content.childNodes;

                        for (var i = 0, len = childNodes.length; i < len; i++) {
                            if(typeof shadow.childNodes[i] === 'undefined'){
                                shadow.innerHTML += childNodes[i].outerHTML || childNodes[i].textContent;
                            } else if (!shadow.childNodes[i].isEqualNode(childNodes[i])) {
                                shadow.childNodes[i].innerHTML = childNodes[i].innerHTML || childNodes[i].textContent;
                            }
                        }

                        let component = templateEl.closest('[wire\\\\3A id]');
                        if(component !== null && component.__livewire !== undefined) {
                            component.__livewire.tearDown()
                            window.__isReExecutingScript = true; // prevents recursion
                            component.__livewire.initialize()
                        }

                        document.addEventListener('alpine:init', () => {
                            Alpine.initTree(shadow);
                        });
                    })(<?php echo time(); ?>);
                </script>
            </div>
            SCRIPT_ECHO;
        });
    }
}
