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

                <script data-reexecute-on-livewire-update>
                    (function(cacheBreaker) {
                        const templateEl = document.getElementById('scoped-element-$currentScriptId');
                        const parentEl = templateEl.parentNode;
                        const content = templateEl.content.cloneNode(true);

                        const shadow = parentEl.shadowRoot || parentEl.attachShadow({ mode:"open" });
                        shadow.innerHTML = '';
                        shadow.append(content);

                        if(typeof Alpine !== 'undefined')
                            Alpine.initTree(shadow);
                        else
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
