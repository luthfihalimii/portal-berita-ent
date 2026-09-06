/**
 * Auto-save draft form artikel ke localStorage.
 *
 * Mencegah kehilangan tulisan jika browser/tab tertutup tak sengaja atau
 * halaman ter-refresh. Draft disimpan per-halaman (create vs edit) berdasarkan
 * URL, dipulihkan otomatis saat form dimuat, dan dibersihkan setelah submit
 * berhasil.
 *
 * Hanya aktif di halaman yang punya form bertanda [data-autosave-form].
 */

const STORAGE_PREFIX = 'haliminews:article-draft:';
const SAVE_INTERVAL_MS = 5000; // simpan berkala tiap 5 detik
const RESTORED_FLAG = 'restored';

export function initArticleAutosave() {
    const form = document.querySelector('[data-autosave-form]');

    if (!form) {
        return;
    }

    const storageKey = STORAGE_PREFIX + window.location.pathname;
    const fields = collectFields(form);

    restoreDraft(form, fields, storageKey);
    attachListeners(form, fields, storageKey);
}

/**
 * Kumpulkan field yang akan di-autosave (semua input/textarea/select bernama,
 * kecuali file, password, dan token CSRF).
 */
function collectFields(form) {
    const excluded = new Set(['_token', '_method']);

    return Array.from(form.querySelectorAll('input[name], textarea[name], select[name]'))
        .filter((el) => {
            if (excluded.has(el.name)) return false;
            if (el.type === 'file' || el.type === 'password') return false;
            return true;
        });
}

/**
 * Pulihkan draft dari localStorage jika ada dan form masih kosong/default.
 */
function restoreDraft(form, fields, storageKey) {
    let saved;

    try {
        saved = JSON.parse(localStorage.getItem(storageKey));
    } catch (e) {
        saved = null;
    }

    if (!saved || !saved.data) {
        return;
    }

    let restored = false;

    fields.forEach((el) => {
        const value = saved.data[el.name];

        if (value === undefined || value === null) {
            return;
        }

        // Untuk form create, hanya restore jika field masih kosong.
        // Untuk radio/checkbox, cocokkan nilainya.
        if (el.type === 'radio' || el.type === 'checkbox') {
            if (el.value === String(value)) {
                el.checked = true;
                restored = true;
            }
        } else if (el.type === 'hidden' && el.id === 'content') {
            // Field konten Trix — set nilai lalu sinkronkan ke editor
            if (el.value === '' || el.value === null) {
                el.value = value;
                restored = true;
            }
        } else if (el.value === '') {
            el.value = value;
            restored = true;
        }
    });

    if (restored) {
        syncTrixEditor(form);
        showNotice(form, 'Draft sebelumnya dipulihkan otomatis.');
        markRestored(form);
    }
}

/**
 * Pasang listener input + interval untuk menyimpan draft.
 */
function attachListeners(form, fields, storageKey) {
    const save = () => saveDraft(fields, storageKey);

    // Simpan saat ada perubahan (debounce ringan via interval juga berjalan)
    form.addEventListener('input', debounce(save, 800));

    // Simpan berkala untuk menangkap perubahan yang lolos dari event input
    const timer = setInterval(save, SAVE_INTERVAL_MS);

    // Bersihkan draft setelah submit (form valid & terkirim)
    form.addEventListener('submit', () => {
        clearInterval(timer);
        localStorage.removeItem(storageKey);
    });

    // Simpan saat user meninggalkan halaman
    window.addEventListener('beforeunload', save);
}

/**
 * Serialisasi nilai field lalu simpan ke localStorage.
 */
function saveDraft(fields, storageKey) {
    const data = {};
    let hasContent = false;

    fields.forEach((el) => {
        if (el.type === 'radio' || el.type === 'checkbox') {
            if (el.checked) {
                data[el.name] = el.value;
                hasContent = true;
            }
        } else {
            data[el.name] = el.value;
            if (el.value && el.value.trim() !== '') {
                hasContent = true;
            }
        }
    });

    // Jangan simpan draft yang benar-benar kosong
    if (!hasContent) {
        return;
    }

    try {
        localStorage.setItem(storageKey, JSON.stringify({
            data,
            savedAt: new Date().toISOString(),
        }));
    } catch (e) {
        // localStorage penuh / tidak tersedia — abaikan dengan tenang
    }
}

/**
 * Sinkronkan nilai hidden input konten ke Trix editor setelah restore.
 */
function syncTrixEditor(form) {
    const hidden = form.querySelector('input#content[type="hidden"]');
    const editor = form.querySelector('trix-editor');

    if (!hidden || !editor) {
        return;
    }

    // Tunggu Trix siap, lalu muat HTML yang dipulihkan
    const load = () => {
        if (editor.editor) {
            editor.editor.loadHTML(hidden.value || '');
        }
    };

    if (editor.editor) {
        load();
    } else {
        editor.addEventListener('trix-initialize', load, { once: true });
    }
}

/**
 * Tampilkan notifikasi kecil bahwa draft dipulihkan.
 */
function showNotice(form, message) {
    const notice = document.createElement('div');
    notice.setAttribute('role', 'status');
    notice.className = 'mb-4 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium flex items-center justify-between';
    notice.innerHTML = `
        <span>${message}</span>
        <button type="button" class="ml-4 text-amber-600 hover:text-amber-900 font-bold" aria-label="Tutup">&times;</button>
    `;

    notice.querySelector('button').addEventListener('click', () => notice.remove());

    form.parentElement.insertBefore(notice, form);

    setTimeout(() => notice.remove(), 8000);
}

function markRestored(form) {
    form.dataset[RESTORED_FLAG] = 'true';
}

function debounce(fn, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, args), wait);
    };
}
