document.addEventListener("DOMContentLoaded", function () {
  console.log("load script.js");

  const api_url = "http://localhost:8000";

  const hearts = document.querySelectorAll(".heart-favorite-btn");

  hearts.forEach((heart) => {
    heart.addEventListener("click", async function () {
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
        await fetch(
          `${api_url}/favorite/edit/removeFavorite/${recipeId}/${favoriteId}`,
          {
            method: "POST",
          }
        )
          .then((res) => res.json())
          .then((data) => {
            console.log(data);
            document.getElementById("recipe-card-" + recipeId).remove();
          })
          .catch((err) => console.error("Failed to remove favorite:\n", err));
      }
    });
  });

  const addRecipeToCompilationBtns = document.querySelectorAll(
    ".compilation_add_recipe_btn"
  );

  addRecipeToCompilationBtns.forEach((addRecipeToCompilationBtn) => {
    addRecipeToCompilationBtn.addEventListener("click", async function () {

      const recipeId = addRecipeToCompilationBtn.dataset.recipe;
      const compilationId = addRecipeToCompilationBtn.dataset.compilation;
      
      await fetch(`${api_url}/compilation/edit/addRecipe/${compilationId}/${recipeId}`)
      .then((res) => res.json())
      .then((data) => console.log(data))
      .catch((err) => console.log(err))
    });
  });
});
