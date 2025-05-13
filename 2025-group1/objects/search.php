<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search | IMDB2</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <h1>Search Functions</h1>
    <script>
    $('document').ready(function()
    {
        const input = $('#searchInput');
        const target = $('#searchOutput');
        let timeout;
        input.on('input', function () {
            query = $(this).val().trim();
            console.log("Input string:", query);

            if (query.length < 5) {
                target.html('').show();
                return;
            }

            clearTimeout(timeout);

            timeout = setTimeout(() => {
                $.get('search_handler.php', {q: query})
                    .done(hints => {
                        target.html(hints).show();
                    })
                    .fail((jqXHR, textStatus, errorThrown) => {
                        target.html(`Error ${jqXHR.status}: ${errorThrown}`).show();
                    });
            }, 100);
        });
    });
</script>
<form >
    <div>
        <label for="searchInput">Search:</label>
        <input type="text" id="searchInput" name="searchInput" placeholder="Search for movies, actors, etc.">
        <p>
            <span id="searchOutput"></span>
        </p>
    </div>
</form>
</body>
</html>