const box = document.querySelector('.box');
const fileInput = document.querySelector('[name="content"');
const selectButton = document.querySelector('label strong');
const fileList = document.querySelector('.file-list');

let droppedFiles = [];

[ 'drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop' ].forEach( event => box.addEventListener(event, function(e) {
    e.preventDefault();
    e.stopPropagation();
}), false );

[ 'dragover', 'dragenter' ].forEach( event => box.addEventListener(event, function(e) {
    box.classList.add('is-dragover');
}), false );

[ 'dragleave', 'dragend', 'drop' ].forEach( event => box.addEventListener(event, function(e) {
    box.classList.remove('is-dragover');
}), false );

box.addEventListener('drop', function(e) {
    droppedFiles = e.dataTransfer.files;
    fileInput.files = droppedFiles;
    updateFileList();
}, false );

fileInput.addEventListener( 'change', updateFileList );

function updateFileList() {
    const filesArray = Array.from(fileInput.files);
    if (filesArray.length > 1) {
        fileList.innerHTML = '<p>Selected files:</p><ul><li>' + filesArray.map(f => f.name).join('</li><li>') + '</li></ul>';
    } else if (filesArray.length == 1) {
        fileList.innerHTML = `<p>Selected file: ${filesArray[0].name}</p>`;
    } else {
        fileList.innerHTML = '';
    }
}
