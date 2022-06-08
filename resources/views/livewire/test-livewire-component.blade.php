@scope
<style>
    p {
        color: blue;
        font-weight: bolder;
    }
</style>

<p>
    These blue paragraphs and button live in a livewire component.
</p>
<p>
    Clicking the button will increment this number: {{ $count }}

    <button wire:click="increment">
        Increment
    </button>
</p>

<script>
    console.log(myValue);
</script>
@endscope
