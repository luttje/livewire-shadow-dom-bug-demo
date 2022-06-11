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

<div>
    <p>
        Clicking the button will increment this number: {{ $count }}
    </p>

    <button wire:click="increment">
        Increment
    </button>
</div>

<script>
    console.log(myValue);
</script>
@endscope
