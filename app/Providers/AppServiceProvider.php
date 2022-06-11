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
                        const templateEl = document.getElementById('scoped-element-$currentScriptId');
                        const parentEl = templateEl.parentNode;
                        const content = templateEl.content.cloneNode(true);

                        const shadow = parentEl.shadowRoot || parentEl.attachShadow({ mode:"open" });

                        // Smart replaces only the changed nodes
                        const morphEl = function(fromEl, toEl) {
                            var childNodes = toEl.childNodes;

                            // TODO: copy attributes

                            for (var i = 0, len = childNodes.length; i < len; i++) {
                                if(typeof fromEl.childNodes[i] === 'undefined'){
                                    fromEl.appendChild(childNodes[i].cloneNode(true));
                                } else if (!fromEl.childNodes[i].isEqualNode(childNodes[i])) {
                                    if (fromEl.childNodes[i].nodeType === Node.TEXT_NODE) {
                                        fromEl.childNodes[i].nodeValue = childNodes[i].nodeValue;
                                    } else if (toEl.childNodes[i].childNodes.length > 0) {
                                        morphEl(fromEl.childNodes[i], childNodes[i]);
                                    } else {
                                        fromEl.childNodes[i].innerHTML = childNodes[i].innerHTML || childNodes[i].textContent;
                                    }
                                }
                            }
                        }

                        morphEl(shadow, content);

                        let component = templateEl.closest('[wire\\\\3A id]');
                        if(component !== null && component.__livewire !== undefined) {
                            component.__livewire.tearDown();
                            let originalRenderState = window.Livewire.components.initialRenderIsFinished;
                            window.Livewire.components.initialRenderIsFinished = false // prevents re-execution of scripts
                            component.__livewire.initialize();

                            setTimeout(function(){
                                window.Livewire.components.initialRenderIsFinished = originalRenderState;
                            }, 0);
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
