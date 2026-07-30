function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
}

function toggleTheme() {
    var html = document.documentElement;
    var isLight = html.getAttribute('data-theme') === 'light';
    var next = isLight ? 'dark' : 'light';
    applyTheme(next);
    localStorage.setItem('theme', next);
}
window.toggleTheme = toggleTheme;
