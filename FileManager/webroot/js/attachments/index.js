var dropzone = new Dropzone(document.body, {
  url: document.getElementById('dropzone-url').textContent,
  previewsContainer: ".table tbody", // You probably don't want the whole body
  // to be clickable to select files
  clickable: false,
  dragstart: function () {
    document.body.classList.add('dragging');
  },
  dragenter: function () {
    document.body.classList.add('dragging');
  },
  dragleave: function (e) {
    console.log(e.target.tagName.toLowerCase());
    console.log(e.target.id);
    if (e.target.id === 'dropzone-target' || e.target.tagName.toLowerCase() === 'body') {
      document.body.classList.remove('dragging');
    }
  },
  dragend: function () {
    document.body.classList.remove('dragging');
  },
  drop: function () {
    document.body.classList.remove('dragging');
  },
});
