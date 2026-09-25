(function () {
    var form = document.querySelector('[data-contact-form]');
    if (!form) {
        return;
    }

    var topicInput = form.querySelector('[data-contact-topic]');
    var pills = form.querySelectorAll('[data-topic]');

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            var id = pill.getAttribute('data-topic');
            pills.forEach(function (other) {
                other.setAttribute('aria-pressed', String(other === pill));
            });
            topicInput.value = id;
        });
    });
})();
