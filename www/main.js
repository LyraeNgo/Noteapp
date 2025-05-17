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



// preference
document.addEventListener("DOMContentLoaded", function () {
  const fontSizeSelect = document.getElementById('fontSize');
  const noteColorPicker = document.getElementById('noteColor');
  const themeSelect = document.getElementById('themeToggle');
  const preview = document.getElementById('notePreview');
  const noteText = preview.querySelector('.note-text');

  if (fontSizeSelect && noteText) {
    fontSizeSelect.addEventListener('change', function () {
      const size = fontSizeSelect.value;
      noteText.style.fontSize = size === 'small' ? '12px' : size === 'large' ? '20px' : '16px';
    });
  }

  if (noteColorPicker && preview) {
    noteColorPicker.addEventListener('input', function () {
      preview.style.backgroundColor = noteColorPicker.value;
    });
  }

  if (themeSelect) {
    themeSelect.addEventListener('change', function () {
      document.body.classList.toggle('dark-mode', themeSelect.value === 'dark');
    });
  }
});

