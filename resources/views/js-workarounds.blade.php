<script>
    let initBugfix = () =>{
        if(window.hasAppliedBugfix)
            return;

        window.hasAppliedBugfix = true;

        let walk;

        walk = function(root, callback) {
            if (callback(root) === false) return

            if(root.shadowRoot){
                let node = root.shadowRoot.firstElementChild

                while (node) {
                    walk(node, callback)
                    node = node.nextElementSibling
                }
            }

            let node = root.firstElementChild

            while (node) {
                walk(node, callback)
                node = node.nextElementSibling
            }
        }

        let overrideComponentWalk = function(callback, callbackWhenNewComponentIsEncountered = el => { }) {
            walk(this.el, el => {
                // Skip the root component element.
                if (el.isSameNode(this.el)) {
                    callback(el)
                    return
                }

                // If we encounter a nested component, skip walking that tree.
                if (el.hasAttribute('wire:id')) {
                    callbackWhenNewComponentIsEncountered(el)

                    return false
                }

                if (callback(el) === false) {
                    return false
                }
            })
        }

        Livewire.hook('component.initialized', (component) => {
            // Override the walk method so it includes shadowRoot content
            component.walk = overrideComponentWalk
        });

        Livewire.hook('element.updating', (from, to) => {
            if(typeof from.shadowRoot === 'undefined')
                return;

            from.shadowRoot.innerHTML = '';
            const parentEl = from;

            // TODO: Let livewire smart replace the shadowRoot children
            // WORKAROUND: We're just replacing the whole content in the shadowRoot
            let count = to.children.length;

            for(let i = 0; i < count; i++){
                let child = to.children[0]; // Pop from bottom of child stack

                if(child.tagName === 'SCRIPT') {
                    const script = document.createElement('script');
                    script.innerHTML = child.innerHTML;

                    to.removeChild(child);
                    child = script;
                }

                parentEl.appendChild(child);
            }
        });

        Livewire.hook('element.updated', (from) => {
            if(typeof from.shadowRoot === 'undefined')
                return;

            // Needed so events are attached to children with wire:click again
            from.__livewire.initialize();
        });
    };

    document.addEventListener('alpine:initializing', initBugfix)
</script>
