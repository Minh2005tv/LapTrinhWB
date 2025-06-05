document.querySelectorAll('.toggle-password').forEach(function(element) {
    element.addEventListener('click', function() {
        const input = document.querySelector(this.getAttribute('toggle'));
        if (input.type === "password") {
            input.type = "text";
            this.classList.remove('bx-show');
            this.classList.add('bx-hide');
        } else {
            input.type = "password";
            this.classList.remove('bx-hide');
            this.classList.add('bx-show');
        }
    });
});

document.querySelector('.button button').addEventListener('click', function(event) {
    event.preventDefault();
    window.location.href = 'login.html';
});