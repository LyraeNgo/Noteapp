window.addEventListener("DOMContentLoaded", () => {
  let createNote = document.querySelector("#create");
  let showForm = document.querySelector("#note-form");
  createNote.addEventListener("click", () => {
    showForm.style.display = "block";
  });

  // View mode switching
  let viewMode = localStorage.getItem('viewMode') || 'grid';

  const gridViewBtn = document.querySelector("#grid-view");
  const listViewBtn = document.querySelector("#list-view");
  const notesContainer = document.querySelector("#notes-container");

  // Apply saved view mode on load
  if (viewMode === 'grid') {
    notesContainer.classList.remove("list-view");
    notesContainer.classList.add("grid-view");
    gridViewBtn.classList.add("active");
    listViewBtn.classList.remove("active");
  } else {
    notesContainer.classList.add("list-view");
    notesContainer.classList.remove("grid-view");
    listViewBtn.classList.add("active");
    gridViewBtn.classList.remove("active");
  }

  gridViewBtn.addEventListener("click", () => {
    notesContainer.classList.remove("list-view");
    notesContainer.classList.add("grid-view");
    gridViewBtn.classList.add("active");
    listViewBtn.classList.remove("active");
    localStorage.setItem('viewMode', 'grid');
  });

  listViewBtn.addEventListener("click", () => {
    notesContainer.classList.add("list-view");
    notesContainer.classList.remove("grid-view");
    listViewBtn.classList.add("active");
    gridViewBtn.classList.remove("active");
    localStorage.setItem('viewMode', 'list');
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

let lastSavedContent = '';
const noteInput = document.querySelector('#noteForm textarea[name="content"]');

noteInput.addEventListener('blur', () => {
  const form = document.getElementById('noteForm');
  const formData = new FormData(form);
  const currentContent = formData.get('content');

  // Kiểm tra nếu nội dung đã thay đổi
  if (currentContent !== lastSavedContent) {
    fetch('Note/save_note.php', { method: 'POST', body: formData })
      .then(() => {
        lastSavedContent = currentContent;
        console.log('Đã lưu note tự động');
        // Hiển thị trạng thái lưu nếu muốn
        location.reload();
      })
      .catch(err => {
        console.error('Lỗi khi lưu note:', err);
      });
  }
});