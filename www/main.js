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

