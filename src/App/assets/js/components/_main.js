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

function closeAllMdActionsMenus(except) {
    document.querySelectorAll('.md-actions.open').forEach(function (wrapper) {
        if (wrapper !== except) {
            wrapper.classList.remove('open');
            wrapper.querySelector('.md-actions-toggle').setAttribute('aria-expanded', 'false');
        }
    });
}

function toggleMdActionsMenu(button) {
    var wrapper = button.closest('.md-actions');
    var isOpen = wrapper.classList.contains('open');
    closeAllMdActionsMenus(wrapper);
    wrapper.classList.toggle('open', !isOpen);
    button.setAttribute('aria-expanded', String(!isOpen));
}
window.toggleMdActionsMenu = toggleMdActionsMenu;

document.addEventListener('click', function (event) {
    if (!event.target.closest('.md-actions')) {
        closeAllMdActionsMenus();
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeAllMdActionsMenus();
    }
});

document.addEventListener('click', function (event) {
    var item = event.target.closest('[data-copy-url]');
    if (!item) {
        return;
    }
    event.preventDefault();

    var url = item.getAttribute('data-copy-url');
    var label = item.querySelector('.label');
    var originalText = label.textContent;

    fetch(url)
        .then(function (response) { return response.text(); })
        .then(function (markdown) { return navigator.clipboard.writeText(markdown); })
        .then(function () {
            label.textContent = 'Copied!';
            setTimeout(function () { label.textContent = originalText; }, 1500);
        })
        .catch(function () {
            navigator.clipboard.writeText(url);
        });

    closeAllMdActionsMenus();
});
