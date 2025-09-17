document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const messages = document.querySelectorAll('.message-content');
        messages.forEach(function(message) {
            message.style.transition = 'opacity 0.5s ease-out';
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 2000);
        });
    }, 5000);
});