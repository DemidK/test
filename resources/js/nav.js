// resources/js/nav.js
class NavigationManager {
    constructor() {
        this.navLinks = document.getElementById('nav-links');
        this.draggedItem = null;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        this.navLinks.querySelectorAll('.draggable').forEach(link => {
            link.setAttribute('draggable', true);
            link.addEventListener('dragstart', this.handleDragStart.bind(this));
            link.addEventListener('dragend', this.handleDragEnd.bind(this));
        });

        this.navLinks.addEventListener('dragover', this.handleDragOver.bind(this));
    }

    handleDragStart(e) {
        this.draggedItem = e.target;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', e.target.innerHTML);
        e.target.classList.add('dragging');
    }

    handleDragEnd(e) {
        e.target.classList.remove('dragging');
        this.draggedItem = null;
        this.saveOrder();
    }

    handleDragOver(e) {
        e.preventDefault();
        const afterElement = this.getDragAfterElement(e.clientX);
        const currentItem = document.querySelector('.dragging');

        if (afterElement == null) {
            this.navLinks.appendChild(currentItem);
        } else {
            this.navLinks.insertBefore(currentItem, afterElement);
        }
    }

    getDragAfterElement(x) {
        const draggableElements = [...this.navLinks.querySelectorAll('.draggable:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = x - box.left - box.width / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            }
            return closest;
        }, { offset: Number.NEGATIVE_INFINITY }).element;
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
        .then(data => console.log('Order updated successfully:', data))
        .catch(error => console.error('Error updating order:', error));
    }
}

document.addEventListener('DOMContentLoaded', () => new NavigationManager());