document.addEventListener("DOMContentLoaded", function () {
  console.log("public/js/script.js");

  const api_url = "http://localhost:8000";

  const hearts = document.querySelectorAll(".heart-favorite-btn");

  hearts.forEach((heart) => {
    heart.addEventListener("click", async function () {
      const recipeId = heart.dataset.recipe;

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
        await fetch(`${api_url}/favorite/edit/removeFavorite/${recipeId}`, {
          method: "POST",
        })
          .then((res) => res.json())
          .then((data) => {
            console.log(data);
            if (document.getElementById("recipe-card-" + recipeId)) {
              if (document.getElementById("recipe-card-" + recipeId).dataset.context == "favorites") {
                document.getElementById("recipe-card-" + recipeId).remove();
              }
            }
            heart.classList.remove("fa-solid");
            heart.classList.add("fa-regular");
          })
          .catch((err) => console.error("Failed to remove favorite:\n", err));
      }
    });
  });

  // add recipe to compilation
  document
    .querySelectorAll(".compilation_add_recipe_btn")
    .forEach((addRecipeToCompilationBtn) => {
      addRecipeToCompilationBtn.addEventListener("click", async function () {
        const recipeId = addRecipeToCompilationBtn.dataset.recipe;
        const compilationId = addRecipeToCompilationBtn.dataset.compilation;

        await fetch(
          `${api_url}/compilation/edit/addRecipe/${compilationId}/${recipeId}`
        )
          .then((res) => {
            res.json();
            console.log(res.status);
          })
          .then((data) => console.log(data))
          .catch((err) => console.log(err));
      });
    });

  // remove recipe from compilation
  document
    .querySelectorAll(".remove_compilation_recipe_btn")
    .forEach((removeRecipeFromCompilationBtn) => {
      removeRecipeFromCompilationBtn.addEventListener(
        "click",
        async function () {
          const recipeId = removeRecipeFromCompilationBtn.dataset.recipe;
          const compilationId =
            removeRecipeFromCompilationBtn.dataset.compilation;

          await fetch(
            `${api_url}/compilation/edit/removeRecipe/${compilationId}/${recipeId}`
          )
            .then((res) => res.json())
            .then((data) => {
              console.log(data);
              document.getElementById("recipe-card-" + recipeId).remove();
            })
            .catch((err) => console.error(err));
        }
      );
    });
});
