(function() {

  class ArticleEditor {

    _editor;

    _filename;

    _titleElement;

    _modalElement;

    constructor(editor, titleElement, modalElement) {
      this._editor = editor;
      this._titleElement = titleElement;
      this._modalElement = modalElement;
    }

    close() {
      this._filename = null;
      this._editor.value = '';
      this._titleElement.innerText = '';
      this._modalElement.style.display = 'none';
    }

    async _load() {
      const res = await fetch('/article/' + encodeURIComponent(this._filename));
      return await res.json();
    }
    _doEdit(title, content) {
      this._titleElement.innerText = title;
      this._editor.value = content;
      this._modalElement.style.display = 'flex';
    }

    async loadArticle(name) {
      this._filename = name;
      const data = await this._load(name);
      this._doEdit(data.title, data.content);
    }

    async save() {
      await fetch('/article/' + encodeURIComponent(this._filename),{
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: this._editor.value
      });
      this.close();
    }
  }

  window.addEventListener('load', function () {

    const editor = new ArticleEditor(
      document.getElementById('aa-editor-area'),
      document.querySelector('.aa-modal-title'),
      document.querySelector('.aa-modal-hover')
    );

    document.querySelectorAll('.aa-article-edit').forEach(btn => {
      btn.addEventListener(
        'click',
        e => editor.loadArticle(e.target.getAttribute('data-filename'))
      );
    });
    document.querySelector('.aa-modal-close').addEventListener('click', () => editor.close());
    document.querySelector('.aa-modal-save').addEventListener('click', async e => {
      e.target.disabled = true;
      e.target.classList.add('aa-save-loading');
      try {
        await editor.save()
      } catch (err) {
        alert('An error occurred saving the article.');
      }
      e.target.disabled = false;
      e.target.classList.remove('aa-save-loading');
    });
  });
})();