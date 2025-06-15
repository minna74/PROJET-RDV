document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let currentDate = new Date();
    let appointments = []; // À remplacer par appel API
    
    // Initialisation
    initCalendar();
    loadTodayAppointments();
    
    // Gestion navigation semaine
    document.getElementById('prev-week').addEventListener('click', function() {
        currentDate.setDate(currentDate.getDate() - 7);
        updateWeekDisplay();
        initCalendar();
    });
    
    document.getElementById('next-week').addEventListener('click', function() {
        currentDate.setDate(currentDate.getDate() + 7);
        updateWeekDisplay();
        initCalendar();
    });
    
    // Initialisation calendrier
    function initCalendar() {
        const tableBody = document.querySelector('#appointments-table tbody');
        tableBody.innerHTML = '';
        
        // Génération des créneaux horaires (8h-18h)
        for(let hour = 8; hour <= 18; hour++) {
            const row = document.createElement('tr');
            
            // Cellule heure
            const timeCell = document.createElement('td');
            timeCell.textContent = `${hour}:00 - ${hour + 1}:00`;
            row.appendChild(timeCell);
            
            // Cellules jours (Lundi à Vendredi)
            for(let day = 1; day <= 5; day++) {
                const date = getDateForWeekDay(day);
                const cell = document.createElement('td');
                
                // Exemple de RDV (à remplacer par données réelles)
                const dayAppointments = getAppointmentsForDate(date, hour);
                
                if(dayAppointments.length > 0) {
                    dayAppointments.forEach(app => {
                        const appDiv = document.createElement('div');
                        appDiv.className = 'appointment-slot';
                        appDiv.innerHTML = `
                            <strong>${app.patientName}</strong><br>
                            <small>${app.reason || 'Consultation'}</small>
                        `;
                        appDiv.addEventListener('click', () => openAppointmentModal(app));
                        cell.appendChild(appDiv);
                    });
                } else {
                    cell.textContent = 'Disponible';
                    cell.style.color = '#6c757d';
                    cell.style.fontStyle = 'italic';
                }
                
                row.appendChild(cell);
            }
            
            tableBody.appendChild(row);
        }
    }
    
    // Chargement RDV du jour
    function loadTodayAppointments() {
        const today = new Date();
        const todayApps = [
            { id: 1, patientName: 'Mohamed Ali', time: '10:00', reason: 'Contrôle annuel' },
            { id: 2, patientName: 'Fatima Zahra', time: '14:30', reason: 'Douleurs abdominales' }
        ];
        
        const container = document.getElementById('today-list');
        container.innerHTML = '';
        
        todayApps.forEach(app => {
            const card = document.createElement('div');
            card.className = 'col-md-6 mb-3';
            card.innerHTML = `
                <div class="card today-card" onclick="openAppointmentModal(${JSON.stringify(app).replace(/"/g, '&quot;')})">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">${app.patientName}</h5>
                            <span class="badge bg-primary">${app.time}</span>
                        </div>
                        <p class="card-text">${app.reason}</p>
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-check"></i> Confirmer
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }
    
    // Fonctions utilitaires
    function getDateForWeekDay(dayOfWeek) {
        // dayOfWeek: 1 (Lundi) à 5 (Vendredi)
        const date = new Date(currentDate);
        const currentDay = date.getDay(); // 0 (Dimanche) à 6 (Samedi)
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
    
    function getAppointmentsForDate(date, hour) {
        // Simulation - à remplacer par appel API
        if(date.getDay() === 2 && hour === 10) { // Mardi 10h
            return [{ 
                id: 1, 
                patientName: 'Karim Benjelloun', 
                reason: 'Suivi traitement',
                date: date,
                hour: `${hour}:00`
            }];
        }
        return [];
    }
    
    // Gestion modal
    window.openAppointmentModal = function(appointment) {
        const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
        document.getElementById('patient-name').value = appointment.patientName;
        document.getElementById('appointment-time').value = 
            `${formatDate(appointment.date)} à ${appointment.hour || appointment.time}`;
        document.getElementById('appointment-reason').value = appointment.reason || '';
        
        if(appointment.status) {
            document.getElementById('appointment-status').value = appointment.status;
        }
        
        modal.show();
    };
    
    // Sauvegarde modifications RDV
    document.getElementById('save-appointment').addEventListener('click', function() {
        // Ici, ajouter la logique de sauvegarde (AJAX)
        alert('Modifications enregistrées !');
        bootstrap.Modal.getInstance(document.getElementById('appointmentModal')).hide();
    });
    
    // Gestion urgence
    document.getElementById('emergency-btn').addEventListener('click', function() {
        // Logique pour ajouter un RDV urgent
        alert('Mode urgence activé - Créneau spécial ajouté');
    });
    
    // Initialisation affichage semaine
    updateWeekDisplay();
});



// Import des modules
import { initAvailability } from './modules/availability.js';
import { initPatients } from './modules/patients.js';
import { initSettings } from './modules/settings.js';

// Gestionnaire de navigation
document.querySelectorAll('[data-section]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const sectionId = this.getAttribute('data-section');
        loadSection(sectionId);
    });
});

// Chargement dynamique des sections
async function loadSection(sectionId) {
    // Masquer toutes les sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.add('d-none');
    });

    // Afficher la section cible
    const section = document.getElementById(`${sectionId}-section`);
    section.classList.remove('d-none');

    // Initialiser le module correspondant
    switch(sectionId) {
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

// Charger la première section par défaut
loadSection('availability');