document.addEventListener("DOMContentLoaded", () => {
    const noteContent = document.getElementById("noteContent");
    const saveNoteBtn = document.getElementById("saveNoteBtn");
    const newNoteBtn = document.getElementById("newNoteBtn");
    const notesList = document.getElementById("notesList");
    let currentNoteId = null;

    // --- 1. LOAD NOTES DARI DATABASE ---
    async function loadNotes() {
        try {
            const response = await fetch('/notes/data'); // Panggil Laravel
            const notes = await response.json();
            renderNotesList(notes);
        } catch (error) {
            console.error("Gagal memuat catatan:", error);
        }
    }

    // --- 2. RENDER LIST (TAMPILKAN) ---
    function renderNotesList(notes) {
        if (!notesList) return;
        notesList.innerHTML = "";

        notes.forEach((note) => {
            const listItem = document.createElement("li");
            listItem.classList.add("note-item");
            
            // Ambil baris pertama sebagai judul
            const title = note.content.split("\n")[0].substring(0, 30) || "Untitled Note";
            
            listItem.innerHTML = `
                <span>${title}</span>
                <div class="note-actions">
                  <button data-id="${note.id}" data-content="${encodeURIComponent(note.content)}" class="view-btn">👀</button>
                  <button data-id="${note.id}" class="delete-btn">🗑️</button>
                </div>
            `;
            notesList.appendChild(listItem);
        });

        // Pasang Event Listener ke tombol baru
        document.querySelectorAll(".view-btn").forEach((btn) => btn.addEventListener("click", viewNote));
        document.querySelectorAll(".delete-btn").forEach((btn) => btn.addEventListener("click", deleteNote));
    }

    // --- 3. SAVE NOTE (SIMPAN KE DATABASE) ---
    if (saveNoteBtn) {
        saveNoteBtn.addEventListener("click", async () => {
            const content = noteContent.value.trim();
            if (!content) {
                alert("Catatan tidak boleh kosong!");
                return;
            }

            try {
                // Kirim data ke Laravel
                const response = await fetch('/notes/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') // Ambil token CSRF kalau ada, atau nanti kita handle di blade
                    },
                    body: JSON.stringify({
                        id: currentNoteId, // Kalau null berarti create baru
                        content: content
                    })
                });

                if (response.ok) {
                    alert(currentNoteId ? "Catatan berhasil diupdate!" : "Catatan berhasil disimpan!");
                    noteContent.value = "";
                    currentNoteId = null;
                    loadNotes(); // Reload list dari database
                }
            } catch (error) {
                console.error("Error saving note:", error);
                alert("Gagal menyimpan catatan.");
            }
        });
    }

    // --- 4. VIEW / EDIT NOTE ---
    function viewNote(e) {
        const btn = e.currentTarget;
        const noteId = btn.getAttribute("data-id");
        const content = decodeURIComponent(btn.getAttribute("data-content"));

        noteContent.value = content;
        currentNoteId = noteId;
        noteContent.placeholder = "Sedang mengedit catatan...";
        noteContent.focus();
    }

    // --- 5. DELETE NOTE ---
    async function deleteNote(e) {
        if (!confirm("Yakin ingin menghapus catatan ini?")) return;

        const noteId = e.currentTarget.getAttribute("data-id");

        try {
            const response = await fetch(`/notes/delete/${noteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                alert("Catatan dihapus!");
                if (currentNoteId == noteId) {
                    noteContent.value = "";
                    currentNoteId = null;
                }
                loadNotes();
            }
        } catch (error) {
            console.error("Gagal menghapus:", error);
        }
    }

    // --- 6. TOMBOL NEW NOTE ---
    if (newNoteBtn) {
        newNoteBtn.addEventListener("click", () => {
            noteContent.value = "";
            currentNoteId = null;
            noteContent.placeholder = "Tulis catatan baru...";
            noteContent.focus();
        });
    }

    // Jalankan saat halaman dibuka
    if (notesList) loadNotes();
});