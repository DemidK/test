class NavigationManager {
    constructor() {
        this.navLinks = document.getElementById('nav-links');
        if (!this.navLinks) return; // Guard clause if element doesn't exist
        
        this.draggedItem = null;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        const draggables = this.navLinks.querySelectorAll('.draggable');
        if (!draggables.length) return; // Guard clause if no draggable items

        draggables.forEach(link => {
            link.addEventListener('dragstart', this.handleDragStart.bind(this));
            link.addEventListener('dragend', this.handleDragEnd.bind(this));
            link.addEventListener('dragover', this.handleDragOver.bind(this));
        });
    }

    handleDragStart(e) {
        this.draggedItem = e.target;
        e.target.classList.add('opacity-50');
    }

    handleDragEnd(e) {
        e.target.classList.remove('opacity-50');
        this.saveOrder();
    }

    handleDragOver(e) {
        e.preventDefault();
        const target = e.target.closest('.draggable');
        if (!target || target === this.draggedItem) return;

        const draggedRect = this.draggedItem.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const nextElement = targetRect.left > draggedRect.left ? target.nextElementSibling : target;

        if (nextElement) {
            this.navLinks.insertBefore(this.draggedItem, nextElement);
        } else {
            this.navLinks.appendChild(this.draggedItem);
        }
    }

    saveOrder() {
        const order = {};
        this.navLinks.querySelectorAll('.draggable').forEach((link, index) => {
            const linkId = link.id.replace('link', '');
            order[linkId] = index + 1;
        });

        fetch('/updateNavOrder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ order })
        })
        .then(response => response.json())
        .then(data => console.log('Order updated:', data))
        .catch(error => console.error('Error updating order:', error));
    }
}

// Initialize only if nav-links element exists
document.addEventListener('DOMContentLoaded', () => {
    const navLinksElement = document.getElementById('nav-links');
    if (navLinksElement) {
        new NavigationManager();
    }
});