import { openAppointmentModal } from './modals.js';

export function initAgenda() {
    renderAgenda();
    document.getElementById('prev-week')?.addEventListener('click', previousWeek);
    document.getElementById('next-week')?.addEventListener('click', nextWeek);
}

function renderAgenda() {
    const tableBody = document.querySelector('#appointments-table tbody');
    tableBody.innerHTML = '';

    for (let hour = 8; hour <= 18; hour++) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${hour}:00 - ${hour + 1}:00</td>
            ${generateDayCells(hour)}
        `;
        tableBody.appendChild(row);
    }
}

function generateDayCells(hour) {
    let cells = '';
    for (let day = 1; day <= 5; day++) {
        const appointment = getAppointmentForSlot(day, hour);
        cells += appointment 
            ? `<td>${formatAppointment(appointment)}</td>`
            : '<td style="color: #6c757d; font-style: italic">Disponible</td>';
    }
    return cells;
}

function formatAppointment(appointment) {
    return `
        <div class="appointment-slot" onclick="openAppointmentModal(${appointment.id})">
            <strong>${appointment.patientName}</strong>
            <small>${appointment.reason || 'Consultation'}</small>
            <span class="badge ${getStatusBadgeClass(appointment.status)}">
                ${getStatusText(appointment.status)}
            </span>
        </div>
    `;
}

// Fonctions utilitaires (à adapter avec vos données réelles)
function getAppointmentForSlot(day, hour) {
    // Exemple de données - à remplacer par un appel API
    if (day === 1 && hour === 9) return {
        id: 1,
        patientName: 'Patient Dupont',
        reason: 'Consultation générale',
        status: 'confirmed'
    };
    if (day === 3 && hour === 11) return {
        id: 2,
        patientName: 'Patient Martin',
        reason: 'Suivi traitement',
        status: 'pending'
    };
    return null;
}

function getStatusBadgeClass(status) {
    const classes = {
        'confirmed': 'bg-success',
        'pending': 'bg-warning text-dark',
        'cancelled': 'bg-secondary',
        'urgent': 'bg-danger'
    };
    return classes[status] || 'bg-primary';
}

function getStatusText(status) {
    const texts = {
        'confirmed': 'Confirmé',
        'pending': 'En attente',
        'cancelled': 'Annulé',
        'urgent': 'Urgent'
    };
    return texts[status] || status;
}

function previousWeek() {
    // Implémentez la logique de changement de semaine
    console.log('Semaine précédente');
}

function nextWeek() {
    // Implémentez la logique de changement de semaine
    console.log('Semaine suivante');
}

// Exposez la fonction au scope global pour les onclick
window.openAppointmentModal = openAppointmentModal;