document.querySelector("#commentForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Empêcher le rechargement de la page

    let content = document.querySelector("#commentContent").value;
    let author = document.querySelector("#commentAuthor").value;

    fetch("/comment/add", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ content: content, author: author })
    })
    .then(response => response.json())
    .then(data => {
        // Ajout du nouveau commentaire dans la liste sans recharger
        let commentList = document.querySelector("#commentList");
        let newComment = document.createElement("li");
        newComment.innerHTML = `<strong>${data.author}</strong>: ${data.content} <em>(${data.createdAt})</em>`;
        commentList.appendChild(newComment);

        // Nettoyage du formulaire
        document.querySelector("#commentForm").reset();
    })
    .catch(error => console.error("Erreur :", error));
});