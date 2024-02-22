<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Article editing application</title>
        <link type="text/css" rel="stylesheet" href="/css/style.css">
        <script type="text/javascript" src="/js/script.js" defer async></script>
    </head>
    <body>
        <div class="aa-container">
            <table class="aa-article-list">
                <tr>
                    <th class="aa-article-ind">#</th>
                    <th class="aa-article-title">Article title</th>
                    <th class="aa-article-actions">Actions</th>
                </tr>
                <?php foreach ($articles as $ind => $article): ?>
                    <tr>
                        <td class="aa-article-ind"><?php echo $ind + 1; ?></td>
                        <td class="aa-article-title"><?php echo $article->getTitle(); ?></td>
                        <td class="aa-article-actions">
                            <button type="button"
                                    class="aa-article-edit"
                                    data-filename="<?php echo $article->getFilename(); ?>"
                            >Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="aa-modal-hover">
            <div class="aa-modal-content">
                <div class="aa-modal-header">
                    <pre class="aa-modal-title"></pre>
                    <button type="button"
                            class="aa-modal-close"
                    >X</button>
                </div>
                <div class="aa-modal-editor">
                    <textarea id="aa-editor-area"></textarea>
                </div>
                <div class="aa-modal-buttons">
                    <button type="button"
                            class="aa-modal-save"
                    >Save changes</button>
                </div>
            </div>
        </div>
    </body>
</html>