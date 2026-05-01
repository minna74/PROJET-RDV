// Gestion de la navigation entre sections
export function initNavigation() {
    document.querySelectorAll('[data-target]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');
            showSection(target);
        });
    });

    // Gestion spéciale du bouton Statistiques
    document.getElementById('stats-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        showSection('dashboard');
        document.getElementById('stats-section')?.scrollIntoView({ behavior: 'smooth' });
        updateActiveNav(this);
    });

    document.getElementById('home-btn')?.addEventListener('click', () => {
        showSection('dashboard');
    });
}

function showSection(sectionId) {
    document.querySelectorAll('.content-container > div').forEach(div => {
        div.classList.add('d-none');
    });
    document.getElementById(`${sectionId}-content`)?.classList.remove('d-none');
    document.getElementById('page-title').textContent = getSectionTitle(sectionId);
    updateActiveNav(document.querySelector(`[data-target="${sectionId}"]`));
}

function getSectionTitle(sectionId) {
    const titles = {
        'dashboard': 'Tableau de bord',
        'availability': 'Gestion des disponibilités',
        'patients': 'Gestion des patients',
        'settings': 'Paramètres du compte'
    };
    return titles[sectionId] || 'Tableau de bord';
}

function updateActiveNav(activeLink) {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    activeLink?.classList.add('active');
}

// Initialisation
initNavigation();
showSection('dashboard');