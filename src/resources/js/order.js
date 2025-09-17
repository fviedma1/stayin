document.addEventListener('DOMContentLoaded', function () {
    const sectionsList = document.querySelector('.sections-grid');
    let draggedItem = null;

    // Función para actualizar los valores de orden
    function updateOrderInputs() {
        const sectionCards = sectionsList.querySelectorAll('.section-card');
        sectionCards.forEach((card, index) => {
            const orderInput = card.querySelector('input[name$="[order]"]');
            if (orderInput) {
                orderInput.value = index + 1;
            }
        });
    }

    // Función para manejar el inicio del arrastre
    function handleDragStart(e) {
        if (e.target && e.target.classList.contains('section-card')) {
            draggedItem = e.target;
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        }
    }

    // Función para manejar el final del arrastre
    function handleDragEnd(e) {
        if (e.target && e.target.classList.contains('section-card')) {
            e.target.classList.remove('dragging');
            updateOrderInputs();
        }
    }

    // Función para manejar el arrastre sobre un elemento
    function handleDragOver(e) {
        e.preventDefault();
        const target = e.target.closest('.section-card');
        if (target && target !== draggedItem) {
            const bounding = target.getBoundingClientRect();
            const offset = e.clientX - bounding.left - (bounding.width / 2);
            if (offset > 0) {
                target.style['border-right'] = `solid 4px ${getComputedStyle(document.documentElement).getPropertyValue('--primary-color')}`;
                target.style['border-left'] = '';
            } else {
                target.style['border-left'] = `solid 4px ${getComputedStyle(document.documentElement).getPropertyValue('--primary-color')}`;
                target.style['border-right'] = '';
            }
        }
    }

    // Función para manejar la salida del arrastre de un elemento
    function handleDragLeave(e) {
        const target = e.target.closest('.section-card');
        if (target) {
            target.style['border-right'] = '';
            target.style['border-left'] = '';
        }
    }

    // Función para manejar el soltar un elemento
    function handleDrop(e) {
        e.preventDefault();
        const target = e.target.closest('.section-card');
        if (target && target !== draggedItem) {
            target.style['border-right'] = '';
            target.style['border-left'] = '';
            const bounding = target.getBoundingClientRect();
            const offset = e.clientX - bounding.left - (bounding.width / 2);
            if (offset > 0) {
                target.insertAdjacentElement('afterend', draggedItem);
            } else {
                target.insertAdjacentElement('beforebegin', draggedItem);
            }
        }
    }

    // Asignar los event listeners
    sectionsList.addEventListener('dragstart', handleDragStart);
    sectionsList.addEventListener('dragend', handleDragEnd);
    sectionsList.addEventListener('dragover', handleDragOver);
    sectionsList.addEventListener('dragleave', handleDragLeave);
    sectionsList.addEventListener('drop', handleDrop);
});