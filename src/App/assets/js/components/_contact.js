// Contact page: topic pills and the client-side "message sent" state.
// The form is not wired to a backend yet, so submit only swaps the panels.
(function () {
    var form = document.querySelector('[data-contact-form]');
    if (!form) {
        return;
    }

    var topicInput = form.querySelector('[data-contact-topic]');
    var pills = form.querySelectorAll('[data-topic]');
    var sent = document.querySelector('[data-contact-sent]');
    var sentTopic = sent.querySelector('[data-contact-sent-topic]');
    var reset = sent.querySelector('[data-contact-reset]');

    function currentPill() {
        return form.querySelector('[data-topic][aria-pressed="true"]');
    }

    function selectTopic(id) {
        pills.forEach(function (pill) {
            pill.setAttribute('aria-pressed', String(pill.getAttribute('data-topic') === id));
        });
        topicInput.value = id;
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            selectTopic(pill.getAttribute('data-topic'));
        });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var pill = currentPill();
        sentTopic.textContent = pill ? pill.textContent : '';
        form.hidden = true;
        sent.hidden = false;
        sent.querySelector('h2').focus({ preventScroll: true });
    });

    reset.addEventListener('click', function () {
        form.reset();
        selectTopic(pills[0].getAttribute('data-topic'));
        sent.hidden = true;
        form.hidden = false;
        form.querySelector('input:not([type="hidden"])').focus();
    });
})();
