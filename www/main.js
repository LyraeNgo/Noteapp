// This view mode wrote by Bac 
    /*
    // View mode switching
    
    const notesContainer = document.querySelector("#notes-container");

  


  gridViewBtn.addEventListener("click", () => {
    console.log("Grid view clicked");
    notesContainer.classList.remove("list-view");
    notesContainer.classList.add("grid-view");
    gridViewBtn.classList.add("active");
    listViewBtn.classList.remove("active");
  });

  listViewBtn.addEventListener("click", () => {
    console.log("List view clicked");
    notesContainer.classList.add("list-view");
    notesContainer.classList.remove("grid-view");
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

*/


  // Dùng JavaScript (auto-save mỗi 300ms)
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

  timeout = setTimeout(() => {
    const form = document.getElementById('noteForm');
    const formData = new FormData(form);
    const currentContent = formData.get('content'); 

    // Chỉ lưu khi nội dung thay đổi
    if (currentContent !== lastSavedContent) {
      fetch('Note/save_note.php', { method: 'POST', body: formData })
        .then(() => {
          lastSavedContent = currentContent;
          location.reload(); // Cập nhật lại nội dung đã lưu
        })
        .catch(err => {
          console.error('Lỗi khi lưu note:', err);
        });
    }
  }, 300);

});

 


function toggleView(view) {
  const container = document.getElementById('notesContainer');

  if (!container) {
    console.error('Không tìm thấy #notesContainer');
    return;
  }

  if (view === 'grid') {
    container.classList.remove('list-group');
    container.classList.add('row', 'row-cols-3');
    gridViewBtn.classList.add("active");
    listViewBtn.classList.remove("active");
  } else if (view === 'list') {
    container.classList.remove('row', 'row-cols-3');
    container.classList.add('list-group');
    listViewBtn.classList.add("active");
    gridViewBtn.classList.remove("active");
  }
}

document.querySelectorAll('.pin_btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const noteId = btn.dataset.noteId;
        
        try {
            const response = await fetch('pin_note.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `note_id=${noteId}`
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                // Update UI
                const icon = btn.querySelector('i');
                icon.classList.toggle('text-primary', result.pinned);
                btn.innerHTML = `
                    ${result.pinned ? 'Bỏ ghim' : 'Ghim'}
                    <i class="fas fa-thumbtack ${result.pinned ? 'text-primary' : ''}"></i>
                `;
                
                // Optionally reorder notes
                location.reload(); // Hoặc cập nhật DOM động
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});

$(document).ready(function() {
  $('.btn-edit-note').on('click', function(e) {
    e.preventDefault();
    const noteId = $(this).data('id');

    $.get('Note/edit_note_modal.php?id=' + noteId, function(data) {
      $('#editNoteModalContainer').html(data);
      $('#editNoteModal').modal('show');
    });
  });
});
