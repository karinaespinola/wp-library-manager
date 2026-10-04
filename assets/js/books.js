document.addEventListener("DOMContentLoaded", () => {
  fetch(`${wlmData.restUrl}books`, {
    method: "POST",

    headers: {
      "Content-Type": "application/json",
      "X-WP-Nonce": wlmData.nonce,
    },

    body: JSON.stringify({
      title: "Domain-Driven Design",
      year: 2003,
      pages: 560,
      genre: "programming",
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
    });

  const books = document.querySelectorAll(".wlm-book");

  books.forEach((book) => {
    book.addEventListener("click", () => {
      book.classList.toggle("is-active");
    });
  });
});
