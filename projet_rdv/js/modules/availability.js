export async function initAvailability(container) {
    // Charger le template HTML
    const response = await fetch('templates/availability.html');
    container.innerHTML = await response.text();

    // Logique spécifique
    const addSlotBtn = container.querySelector('#add-slot');
    addSlotBtn.addEventListener('click', () => {
        console.log('Ajouter un créneau');
        // Implémentation...
    });

    // Exemple de données dynamiques
    const timeSlots = await fetch('/api/availability').then(res => res.json());
    renderTimeSlots(container, timeSlots);
}

function renderTimeSlots(container, slots) {
    const tableBody = container.querySelector('#slots-table tbody');
    // Remplir le tableau...
}