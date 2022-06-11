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
    // This is needed because calling a component's tearDown and initialize will re-execute scripts
    if (!window.executedTestLivewireComponent) {
        window.executedTestLivewireComponent = true;
        console.log(myValue);
    }
</script>
@endscope
