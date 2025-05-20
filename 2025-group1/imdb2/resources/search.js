const version = "v1";
console.log("search.js " + version + " loaded successfully");

$(document).ready(function () {
	const $input = $('#search-input');
	const $preview = $('#search-output');

	let debounceTimeout; // Initialise variable for clearTimeout

	$input.on('input', function () {
		const query = $input.val().trim();
		clearTimeout(debounceTimeout);
		console.log("Now searching for: " + query); // Log input for debug

		if (query.length < 3) {
			$preview.empty();
			$preview.append("");
			$preview.show();
			return;
		}
		console.log(window.location)
		
		debounceTimeout = setTimeout(function () {
			$.ajax({
				url: 'resources/search.php',
				method: 'GET',
				data: {
					q: query,
				},
				dataType: 'json',
				success: function (data) {
					console.log(data); // Log data for debug
					$preview.empty();  // Clear previous results

					const results = data && Array.isArray(data.results) ? data.results : [];
					if (results.length > 0) {
						$preview.append("<h3>Suggestions:</h3><br>");
						results.slice(0, 5).forEach(item => {
							const typeLabel = item.table_name === "title_basics_trim" ? "Movie/Show" : item.table_name === "name_basics_trim" ? "Person" : item.table_name;
							const typeClass = item.table_name === "title_basics_trim" ? "title" : item.table_name === "name_basics_trim" ? "person" : item.table_name;
							const resultHtml = `
								<strong> <a href="./resources/page.php?q=${item.id}" target="_blank">${item.primary_name}</a></strong><br>
								${typeLabel}<br>
								<hr>
							`;
							console.log("Added result: " + (item.primary_name));
							$preview.append(resultHtml);
						});
						const extraResults = `<strong>See all ${(results.length)} results!</strong>`
						$preview.append("<hr><br>")
						$preview.append("<br><a href='find.php?q=" + query + "'>" + extraResults + "!</a>");
						$preview.show();
					} else {
						$preview.append("No results found.").show();
					}
				},
				error: function () {
					$preview.html('<div class="list-group-item text-danger">Error fetching results</div>').show();
				}
			});
		}, 300);
	});
	$input.on('keydown', function (e) {
		if (e.key === 'Enter') {
			const query = $input.val().trim();
			if (query.length >= 1) {
				window.location.href = 'find.php?q=' + encodeURIComponent(query);
				console.log("Redirecting to find.php with query: " + query);
			}
		}
	});
});
