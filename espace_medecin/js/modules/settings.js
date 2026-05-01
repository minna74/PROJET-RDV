export async function initSettings(container) {
    const response = await fetch('templates/settings.html');
    container.innerHTML = await response.text();

    // Gestion des préférences
    container.querySelector('#language-select').addEventListener('change', (e) => {
        localStorage.setItem('lang', e.target.value);
    });
}