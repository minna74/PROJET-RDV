export async function initPatients(container) {
    const response = await fetch('templates/patients.html');
    container.innerHTML = await response.text();

    // Initialiser la recherche
    container.querySelector('#patient-search').addEventListener('input', (e) => {
        filterPatients(e.target.value);
    });

    // Charger les patients
    const patients = await fetch('/api/patients').then(res => res.json());
    renderPatients(container, patients);
}