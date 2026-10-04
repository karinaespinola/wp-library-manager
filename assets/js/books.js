document.addEventListener('DOMContentLoaded', () => {
    const books = document.querySelectorAll('.wlm-book');

    books.forEach((book) => {
        book.addEventListener('click', () => {
            book.classList.toggle('is-active');
        });
    });
});