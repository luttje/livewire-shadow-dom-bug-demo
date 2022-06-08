# Demo of Livewire and AlpineJS Bugs(?)

1. `composer install`
2. `npm install`
3. `npm run dev`
4. `cp .env.example .env`
5. `php artisan key:generate`
6. `php artisan serve`
7. See the parent page at: [resources/views/welcome.blade.php](resources/views/welcome.blade.php)
8. See how the component is scoped using [custom blade directives](app/Providers/AppServiceProvider.php) at: [resources/views/livewire/test-livewire-component.blade.php](resources/views/livewire/test-livewire-component.blade.php)
9. Notice how this workaround is needed for Livewire and AlpineJS to recognize and respond to changes (click the "Increment" button): [resources/views/js-workarounds.blade.php](resources/views/js-workarounds.blade.php)
