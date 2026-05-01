
// doctor.js corrigé : sans simulation de données factices
document.addEventListener('DOMContentLoaded', function () {
    let currentDate = new Date();

    updateWeekDisplay();

    document.getElementById('prev-week').addEventListener('click', function () {
        currentDate.setDate(currentDate.getDate() - 7);
        updateWeekDisplay();
    });

    document.getElementById('next-week').addEventListener('click', function () {
        currentDate.setDate(currentDate.getDate() + 7);
        updateWeekDisplay();
    });

    function getDateForWeekDay(dayOfWeek) {
        const date = new Date(currentDate);
        const currentDay = date.getDay();
        const diff = dayOfWeek - (currentDay === 0 ? 7 : currentDay) + 1;
        date.setDate(date.getDate() + diff);
        return date;
    }

    function updateWeekDisplay() {
        const monday = getDateForWeekDay(1);
        const friday = getDateForWeekDay(5);
        document.getElementById('current-week').textContent =
            `Semaine du ${formatDate(monday)} au ${formatDate(friday)}`;
    }

    function formatDate(date) {
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
    }

    window.openAppointmentModal = function (appointment) {
        const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
        document.getElementById('patient-name').value = appointment.patientName;
        document.getElementById('appointment-time').value =
            `${formatDate(appointment.date)} à ${appointment.hour || appointment.time}`;
        document.getElementById('appointment-reason').value = appointment.reason || '';

        if (appointment.status) {
            document.getElementById('appointment-status').value = appointment.status;
        }

        modal.show();
    };

    document.getElementById('save-appointment').addEventListener('click', function () {
        alert('Modifications enregistrées !');
        bootstrap.Modal.getInstance(document.getElementById('appointmentModal')).hide();
    });

    document.getElementById('emergency-btn').addEventListener('click', function () {
        alert('Mode urgence activé - Créneau spécial ajouté');
    });
});

// Import des modules
import { initAvailability } from './modules/availability.js';
import { initPatients } from './modules/patients.js';
import { initSettings } from './modules/settings.js';

document.querySelectorAll('[data-section]').forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const sectionId = this.getAttribute('data-section');
        loadSection(sectionId);
    });
});

async function loadSection(sectionId) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.add('d-none');
    });

    const section = document.getElementById(`${sectionId}-section`);
    section.classList.remove('d-none');

    switch (sectionId) {
        case 'availability':
            await initAvailability(section);
            break;
        case 'patients':
            await initPatients(section);
            break;
        case 'settings':
            await initSettings(section);
            break;
    }
}

loadSection('availability');