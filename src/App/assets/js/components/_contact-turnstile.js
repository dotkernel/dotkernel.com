const ERROR_CLASS = 'contact-field-error';

const REQUIRED_FIELDS = {
    name: 'Name is required.',
    email: 'Work email is required.',
    message: 'Message is required.',
};
const INVALID_EMAIL = 'Enter a valid email address.';
const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const TURNSTILE_PENDING = 'Please wait for the verification to finish, then send again.';

function showError(element, message) {
    const error = document.createElement('p');
    error.className = `contact-error ${ERROR_CLASS}`;
    error.textContent = message;
    element.insertAdjacentElement('afterend', error);
}

function validateContactForm(form) {
    form.querySelectorAll(`.${ERROR_CLASS}, .contact-server-error`).forEach((el) => el.remove());

    let firstInvalid = null;
    Object.entries(REQUIRED_FIELDS).forEach(([name, message]) => {
        const field = form.elements.namedItem(name);
        if (!field) {
            return;
        }
        const value = field.value.trim();
        let error = null;
        if (value === '') {
            error = message;
        } else if (name === 'email' && !EMAIL_PATTERN.test(value)) {
            error = INVALID_EMAIL;
        }
        if (error) {
            showError(field, error);
            firstInvalid = firstInvalid || field;
        }
    });

    if (firstInvalid) {
        firstInvalid.focus();
        return false;
    }

    const widget = document.getElementById('contact-turnstile');
    const token = form.querySelector('[name="cf-turnstile-response"]');
    if (widget && (!token || token.value === '')) {
        showError(widget, TURNSTILE_PENDING);
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-contact-form]');
    if (!form) {
        return;
    }

    form.addEventListener('submit', (event) => {
        if (!validateContactForm(form)) {
            event.preventDefault();
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }
    });
});
