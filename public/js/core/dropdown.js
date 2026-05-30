/**
 * Custom Dropdown UI component for JAMKOT
 * Automatically transforms standard selects into premium styled custom dropdowns.
 */
document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    initCustomDatepickers();
});

function initCustomSelects() {
    const selects = document.querySelectorAll('select.filter-input');
    selects.forEach(select => {
        // Prevent double initialization
        if (select.dataset.customSelectInitialized) return;
        select.dataset.customSelectInitialized = 'true';

        // Hide native select
        select.style.display = 'none';

        // Create container
        const container = document.createElement('div');
        container.className = 'custom-select-container';
        
        // Insert container before select
        select.parentNode.insertBefore(container, select);
        container.appendChild(select); // move select inside container

        // Create trigger button
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'custom-select-btn';
        
        const label = document.createElement('span');
        label.className = 'custom-select-label';
        
        const arrow = document.createElement('i');
        arrow.className = 'fa-solid fa-chevron-down custom-select-arrow';
        
        btn.appendChild(label);
        btn.appendChild(arrow);
        container.appendChild(btn);

        // Create dropdown menu
        const dropdown = document.createElement('div');
        dropdown.className = 'custom-select-dropdown';
        container.appendChild(dropdown);

        // Populate options
        const syncCustomOptions = () => {
            dropdown.innerHTML = '';
            const options = select.querySelectorAll('option');
            options.forEach(opt => {
                const optDiv = document.createElement('div');
                optDiv.className = 'custom-select-option';
                optDiv.textContent = opt.textContent;
                optDiv.dataset.value = opt.value;
                
                if (opt.selected) {
                    optDiv.classList.add('selected');
                    label.textContent = opt.textContent;
                }

                optDiv.addEventListener('click', (e) => {
                    e.stopPropagation();
                    select.value = opt.value;
                    
                    // Trigger native change event
                    const event = new Event('change', { bubbles: true });
                    select.dispatchEvent(event);

                    // Sync custom display
                    container.querySelectorAll('.custom-select-option').forEach(o => o.classList.remove('selected'));
                    optDiv.classList.add('selected');
                    label.textContent = opt.textContent;

                    // Close dropdown
                    container.classList.remove('open');
                });

                dropdown.appendChild(optDiv);
            });
        };

        syncCustomOptions();

        // Toggle dropdown on button click
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Close all other open custom selects
            document.querySelectorAll('.custom-select-container.open').forEach(openContainer => {
                if (openContainer !== container) {
                    openContainer.classList.remove('open');
                }
            });

            container.classList.toggle('open');
        });

        // Listen for changes on native select to keep in sync (e.g. reset button)
        select.addEventListener('change', () => {
            const selectedOpt = select.querySelector(`option[value="${select.value}"]`);
            if (selectedOpt) {
                label.textContent = selectedOpt.textContent;
                container.querySelectorAll('.custom-select-option').forEach(o => {
                    if (o.dataset.value === select.value) {
                        o.classList.add('selected');
                    } else {
                        o.classList.remove('selected');
                    }
                });
            }
        });
    });

    // Close any open dropdown when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-select-container.open').forEach(container => {
            container.classList.remove('open');
        });
    });
}

function initCustomDatepickers() {
    if (typeof flatpickr !== 'function') return;

    const dateInputs = document.querySelectorAll('input[type="date"].filter-input');
    dateInputs.forEach(input => {
        flatpickr(input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'j F Y',
            disableMobile: true,
            static: true, // Make calendar popup scroll relative to the input container
            monthSelectorType: 'static', // Avoid native browser select element contrast bug
        });
    });
}
