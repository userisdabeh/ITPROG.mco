document.addEventListener('DOMContentLoaded', () => {
    const uploadDocumentBtn = document.getElementById('upload-document-btn');
    const uploadDocumentInput = document.getElementById('pet-documents');

    uploadDocumentBtn.addEventListener('click', () => {
        uploadDocumentInput.click();
    });
});