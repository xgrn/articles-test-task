<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Article editing application</title>
        <link type="text/css" rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <div class="aa-container">
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <th>Article title</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($articles as $ind => $article): ?>
                    <tr>
                        <td><?php echo $ind; ?></td>
                        <td><?php echo $article['title']; ?></td>
                        <td><button type="button">Edit</button></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </body>
</html>