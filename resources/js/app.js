import 'trix';
import 'trix/dist/trix.css';
import { initArticleAutosave } from './article-autosave.js';

/**
 * Nonaktifkan caption default attachment (nama file + ukuran) pada gambar.
 *
 * Secara bawaan, Trix menampilkan "images.jpeg • 19.36 KB" di bawah gambar
 * dan menyimpannya ke konten. Untuk konten berita, caption semacam itu tidak
 * relevan — user tetap bisa mengetik caption sendiri jika mau.
 */
if (window.Trix?.config?.attachments?.preview) {
    window.Trix.config.attachments.preview.caption = { name: false, size: false };
}

// Inisialisasi auto-save draft form artikel (create & edit)
document.addEventListener('DOMContentLoaded', initArticleAutosave);

/**
 * Trix Rich Text Editor - Attachment Upload Handler
 *
 * Meng-handle upload gambar (drag & drop / paste / tombol attachment)
 * ke endpoint Laravel, lalu menyematkan URL permanen ke konten.
 */
document.addEventListener('trix-attachment-add', function (event) {
    const attachment = event.attachment;

    if (!attachment.file) {
        return;
    }

    uploadTrixAttachment(attachment);
});

/**
 * Upload file attachment ke server dengan progress indicator.
 */
function uploadTrixAttachment(attachment) {
    const editor = attachment.attachmentManager?.editor ?? document.querySelector('trix-editor')?.editor;
    const uploadUrl = document.querySelector('trix-editor')?.dataset.uploadUrl ?? '/admin/articles/upload-image';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const formData = new FormData();
    formData.append('image', attachment.file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', uploadUrl, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken ?? '');
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.upload.addEventListener('progress', function (event) {
        if (event.lengthComputable) {
            const progress = (event.loaded / event.total) * 100;
            attachment.setUploadProgress(progress);
        }
    });

    xhr.addEventListener('load', function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            const response = JSON.parse(xhr.responseText);
            attachment.setAttributes({
                url: response.url,
                href: response.url,
            });
        } else {
            let message = 'Upload gambar gagal.';

            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    message = response.message;
                }
            } catch (e) {
                // response bukan JSON
            }

            attachment.remove();
            alert(message);
        }
    });

    xhr.addEventListener('error', function () {
        attachment.remove();
        alert('Upload gambar gagal. Periksa koneksi Anda.');
    });

    xhr.send(formData);
}
