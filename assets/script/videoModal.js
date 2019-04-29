var btns = document.getElementsByClassName('modal_opener');
var modal = document.querySelector('.modal');

function attachModalListeners(modalElm) {
  modalElm.querySelector('.close_modal').addEventListener('click', toggleModal);
  modalElm.querySelector('.overlay').addEventListener('click', toggleModal);
}

function detachModalListeners(modalElm) {
  modalElm.querySelector('.close_modal').removeEventListener('click', toggleModal);
  modalElm.querySelector('.overlay').removeEventListener('click', toggleModal);
}

function toggleModal(vid, vidTitle) {
  var currentState = modal.style.display;

  // If modal is visible, hide it. Else, display it.
  if (currentState === 'none') {
    vid = "https://player.vimeo.com/video/"+vid;

    document.getElementById('video-title').innerHTML = vidTitle;
    document.getElementById('video-embed').src = vid;

    modal.style.display = 'block';
    attachModalListeners(modal);
  } else {
    modal.style.display = 'none';
    detachModalListeners(modal);  
    document.getElementById('video-embed').src = "";
  }
}

for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener('click', function () {
        let vid = this.getAttribute("vid-src");
        let vidTitle = this.getAttribute("vid-title");
        toggleModal(vid, vidTitle);
    });
}