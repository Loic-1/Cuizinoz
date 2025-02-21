document.addEventListener("DOMContentLoaded", function () {
  console.log("load script.js");

  const api_url = "http://localhost:8000";

  const hearts = document.querySelectorAll(".heart-favorite-btn");

  hearts.forEach((heart) => {
    heart.addEventListener("click", async function () {
      console.log("Click");

      const recipeId = heart.dataset.recipe;
      const favoriteId = heart.dataset.favorite;

      if (heart.classList.contains("fa-regular")) {

        await fetch(`${api_url}/favorite/edit/addFavorite/${recipeId}`, {
          method: "POST",
        })
          .then((res) => res.json())
          .then((data) => {
            console.log(data);
            heart.classList.remove("fa-regular");
            heart.classList.add("fa-solid");
          })
          .catch((err) => console.error("Failed to add favorite:\n", err));
      } else {

        await fetch(`${api_url}/favorite/edit/removeFavorite/${recipeId}/${favoriteId}`, {
          method: "POST",
        })
          .then((res) => res.json())
          .then((data) => {
            console.log(data);
            heart.classList.remove("fa-solid");
            heart.classList.add("fa-regular");
          })
          .catch((err) => console.error("Failed to remove to favorite:\n", err));
      }
    });
  });
});
