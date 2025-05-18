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

// Xử lý xóa ghi chú
$(document).on('click', '.delete-note', function(e) {
    e.preventDefault();
    const noteId = $(this).data('note-id');
    
    if (confirm('Are you sure you want to delete this note?')) {
        $.ajax({
            url: '/Note/delete_note.php',
            method: 'POST',
            data: { note_id: noteId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Xóa ghi chú khỏi giao diện
                    $(`[data-note-id="${noteId}"]`).closest('.note-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.message || 'Failed to delete note');
                }
            },
            error: function() {
                alert('An error occurred while deleting the note');
            }
        });
    }
});


// search
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById('searchInput');
  const notesContainer = document.getElementById('notes-container');

  let debounceTimeout;

  searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimeout);

    debounceTimeout = setTimeout(() => {
      const query = searchInput.value.trim();

      fetch(`www/Note/search_notes.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            notesContainer.innerHTML = '<p>Error: ' + data.error + '</p>';
            return;
          }

          if (data.length === 0) {
            notesContainer.innerHTML = '<p>No matching notes found.</p>';
            return;
          }

          // Build HTML from notes
          let html = '';
          data.forEach(note => {
            html += `
              <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 note-item">
                <div class="border rounded p-3 shadow-sm h-100 bg-white">
                  <div class="font-weight-bold text-truncate mb-2" style="max-width: 100%;">${escapeHtml(note.tieu_de)}</div>
                  <div class="text-muted text-truncate" style="max-width: 100%;">${escapeHtml(note.noi_dung)}</div>
                </div>
              </div>`;
          });

          notesContainer.innerHTML = html;
        })
        .catch(err => {
          notesContainer.innerHTML = '<p>Error fetching notes.</p>';
          console.error(err);
        });
    }, 300); // debounce 300ms
  });

  // Helper to escape HTML special chars
  function escapeHtml(text) {
    return text.replace(/[&<>"']/g, function(m) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      }[m];
    });
  }
});
