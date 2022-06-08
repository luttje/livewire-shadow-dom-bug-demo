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
        Blade::directive('scope', function () {
            return <<<SCRIPT_ECHO
            <div>
                <?php
                \$tagConfigs = [
                    [
                        'tag' => 'noscript'
                    ],
                    [
                        'tag' => 'template',
                        'style' => 'display:none'
                    ],
                ];
                foreach (\$tagConfigs as \$tagConfig) {
                    extract(\$tagConfig);

                    if(isset(\$style))
                        echo "<\$tag style=\"\$style\">";
                    else
                        echo "<\$tag>";
                ?>
            SCRIPT_ECHO;
        });
        Blade::directive('endscope', function () {
            return <<<SCRIPT_ECHO
                <?php
                    echo "</\$tag>";
                } ?>

                <script>
                (function(thisScript) {
                    const templateEl = thisScript.previousElementSibling;
                    const parentEl = templateEl.parentNode;
                    const content = templateEl.content.cloneNode(true);

                    parentEl.innerHTML = '';
                    const shadow = parentEl.shadowRoot || parentEl.attachShadow({ mode:"open" });
                    shadow.append(content);

                    document.addEventListener('alpine:init', () => {
                        Alpine.initTree(shadow);
                    });
                })(document.currentScript);
                </script>
            </div>
            SCRIPT_ECHO;
        });
    }
}
