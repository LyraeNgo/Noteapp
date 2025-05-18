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
  let noteText = null;
  if (preview) {
    noteText = preview.querySelector('.note-text');
  }

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

// Xử lý xóa ghi chú (làm lại từ đầu)
$(document).off('click', '.delete-note'); // Xóa event cũ nếu có
$(document).on('click', '.delete-note', function(e) {
    e.preventDefault();
    const noteId = $(this).data('note-id');
    console.log('Delete clicked, noteId:', noteId);
    if (!noteId) {
        alert('Note ID không hợp lệ!');
        return;
    }
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
                    showToast('Note deleted successfully!', 'success');
                } else {
                    showToast(response.message || 'Failed to delete note', 'error');
                }
            },
            error: function(xhr) {
                let msg = 'An error occurred while deleting the note';
                if (xhr.responseText) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        msg = res.message || msg;
                    } catch(e) {}
                }
                showToast(msg, 'error');
            }
        });
    }
});


$(document).on('click', '.btn-edit-note', function(e) {
  e.preventDefault();
  const noteId = $(this).data('id');

  $.get('Note/edit_note_modal.php?id=' + noteId, function(data) {
    $('#editNoteModalContainer').html(data);
    $('#editNoteModal').modal('show');
  });
});

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

document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById('searchInput');
  const notesContainer = document.getElementById('notes-container');
  if (!searchInput || !notesContainer) return;
  let debounceTimeout;

  searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      const query = searchInput.value.trim();
      fetch('/Note/search_notes.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            notesContainer.innerHTML = '<p class="text-danger">' + escapeHtml(data.error) + '</p>';
            return;
          }
          if (data.length === 0) {
            notesContainer.innerHTML = '<p>No matching notes found.</p>';
            return;
          }
          let html = '';
          data.forEach(note => {
            html += `
              <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 note-item">
                <div class="border rounded p-3 shadow-sm h-100" style="background-color: ${note.color || (window.userPreference && window.userPreference.noteColor) || '#fff'}; font-size: ${(window.userPreference && window.userPreference.fontSize) || '16px'};">
                  <!-- Title + Pin -->
                  <div class="font-weight-bold text-truncate mb-2 d-flex justify-content-between align-items-start" style="max-width: 100%;">
                    <div class="text-truncate">
                      ${escapeHtml(note.tieu_de)}
                      ${note.is_pinned ? '<i class="fa-solid fa-thumbtack"></i>' : ''}
                    </div>
                  </div>

                  <!-- Content + Kebab -->
                  <div class="d-flex justify-content-between align-items-start">
                    <!-- Note content -->
                    <div class="text-muted text-truncate pr-2" style="max-width: 90%;">
                      ${escapeHtml(note.noi_dung)}
                    </div>

                    <!-- Kebab dropdown -->
                    <div class="dropdown">
                      <button class="btn btn-sm btn-light p-1" type="button" data-toggle="dropdown" aria-expanded="false">
                        <span class="text-dark"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                      </button>
                      <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="Note/pin_note.php?id=${note.id}">
                          ${note.is_pinned ? 
                            '<i class="fa-solid fa-circle-xmark"></i> Unpin' : 
                            '<i class="fa-solid fa-thumbtack"></i> Pin'}
                        </a>
                        <a class="dropdown-item btn-edit-note" href="#" data-id="${note.id}">
                          <i class="fa-solid fa-pen"></i> Modify
                        </a>
                        <a class="dropdown-item text-danger delete-note" href="#" data-note-id="${note.id}">
                          <i class="fa-solid fa-trash-can"></i> Delete
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>`;
          });
          notesContainer.innerHTML = html;
          // Áp dụng lại dark mode nếu cần
          if (window.userPreference && window.userPreference.theme === 'dark') {
            document.body.classList.add('dark-mode');
          } else {
            document.body.classList.remove('dark-mode');
          }
        })
        .catch(err => {
          notesContainer.innerHTML = '<p class="text-danger">Error fetching notes.</p>';
          console.error(err);
        });
    }, 300);
  });
  function escapeHtml(text) {
    if (!text) return '';
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

$(document).on('click', '.pin-note', function(e) {
  e.preventDefault();
  const noteId = $(this).data('note-id');
  
  if (confirm('Are you sure you want to pin this note?')) {
    $.ajax({
      url: '/Note/pin_note.php',
      method: 'POST',
      data: { note_id: noteId },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Update UI
          const icon = $(this).find('i');
          icon.toggleClass('text-primary');
          $(this).text(response.pinned ? 'Unpin' : 'Pin');
        } else {
          showToast(response.message || 'Failed to pin note', 'error');
        }
      },
      error: function() {
        showToast('An error occurred while pinning the note', 'error');
      }
    });
  }
});

function showToast(message, type = 'success') {
  const toastId = 'toast-' + Date.now();
  const bg = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
  const html = `
    <div id="${toastId}" class="toast ${bg} text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2500" style="min-width: 250px;">
      <div class="toast-body d-flex justify-content-between align-items-center">
        <span>${message}</span>
        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close" style="background: none; border: none; font-size: 1.2rem;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>`;
  $('#toast-container').append(html);
  const $toast = $('#' + toastId);
  $toast.toast({ delay: 2500 });
  $toast.toast('show');
  $toast.on('hidden.bs.toast', function () {
    $toast.remove();
  });
}

// Tự động focus vào ô tiêu đề khi mở modal tạo note
$(document).ready(function() {
  $('#myModal').on('shown.bs.modal', function () {
    $('#title').trigger('focus');
  });
});
// Tự động focus vào ô tiêu đề khi mở modal sửa note
$(document).on('shown.bs.modal', '#editNoteModal', function () {
  $('#editNoteModal').find('#title').trigger('focus');
});
// Cho phép nhấn Enter để submit form khi đang ở ô tiêu đề hoặc nội dung
$(document).on('keydown', '#title, #content', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    $(this).closest('form').submit();
  }
});
