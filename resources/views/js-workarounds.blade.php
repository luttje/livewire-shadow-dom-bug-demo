<script>
    let initBugfix = () =>{
        /// START OF WORKAROUND
        if(window.hasAppliedBugfix)
            return;

        window.hasAppliedBugfix = true;

        let walk;

        walk = function(root, callback) {
            if (callback(root) === false) return

            // Needed to activate wire: attributes in shadowRoot content
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
        /// END OF WORKAROUND

        // Re-executes scripts on changes to the script tag (if they are marked data-re-execute-on-livewire-update)
        Livewire.hook('element.updated', (from, component) => {
            if(from.tagName !== 'SCRIPT')
                return;

            if(!from.hasAttribute('data-re-execute-on-livewire-update'))
                return;

            const parentEl = from.parentElement;
            const script = document.createElement('script');
            // Copy the attributes of the existing script tag
            [...from.attributes].forEach( attr => { script.setAttribute(attr.nodeName ,attr.nodeValue) })
            script.innerHTML = from.innerHTML;

            parentEl.replaceChild(script, from);
        });
    };

    document.addEventListener('alpine:initializing', initBugfix)
</script>
