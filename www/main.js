window.addEventListener("DOMContentLoaded",()=>{
    let createNote= document.querySelector("#create")
    let showForm= document.querySelector("#note-form")
    createNote.addEventListener("click",()=>{
        showForm.style.display="block";
    })

    // View mode switching
    const gridViewBtn = document.querySelector("#grid-view");
    const listViewBtn = document.querySelector("#list-view");
    const notesContainer = document.querySelector("#notes-container");

    console.log("Grid button:", gridViewBtn);
    console.log("List button:", listViewBtn);
    console.log("Notes container:", notesContainer);

    gridViewBtn.addEventListener("click", () => {
        console.log("Grid view clicked");
        notesContainer.classList.remove("list-view");
        gridViewBtn.classList.add("active");
        listViewBtn.classList.remove("active");
    });

    listViewBtn.addEventListener("click", () => {
        console.log("List view clicked");
        notesContainer.classList.add("list-view");
        listViewBtn.classList.add("active");
        gridViewBtn.classList.remove("active");
    });
});
