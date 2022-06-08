@scope
<style>
    p {
        color: red;
    }
</style>

<p>
    These red paragraphs live in a simple blade component
</p>
<div x-data="{alpineTest: 'This is an alpinejs test.'}">
    <input type="text" x-model="alpineTest">
    <p x-text="alpineTest">

    </p>
</div>
@endscope
