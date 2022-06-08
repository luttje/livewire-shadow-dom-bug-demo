<!DOCTYPE html>
<html lang="en">
<head>
    <title>Main Page</title>

    @livewireStyles
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>

    @include('js-workarounds')
</head>
<body>
    <script>
        let myValue = 'a value here';
    </script>
    <p>
        This paragraph lives in the body
    </p>

    <x-test-component></x-test-component>
    <livewire:test-livewire-component></livewire:test-livewire-component>

    @livewireScripts

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
