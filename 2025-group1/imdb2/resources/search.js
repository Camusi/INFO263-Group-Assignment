console.log("search.js loaded successfully");

$(document).ready(function () {
	const $input = $('#search-input');
	const $preview = $('#search-output');

	let debounceTimeout; // Initialise variable for clearTimeout

	$input.on('input', function () {
		const query = $input.val().trim();
		clearTimeout(debounceTimeout);

		if (query.length < 3) {
			$preview.empty();
			$preview.append("");
			$preview.show();
			return;
		}

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
							let coverImage = '';
							$.ajax({
								url: `resources/cover-image.php?q=${item.id}`,
								method: 'GET',
								dataType: 'json',
								async: false,
								success: function (imgData) {
									if (imgData && imgData.cover_image) {
										coverImage = `<img src="${imgData.cover_image}" style="margin-right:10px;vertical-align:middle;width:50px;height:75px;">`;
									}
								}
							});
							const resultHtml = `
								${coverImage}
								<strong> <a href="./resources/page.php?q=${item.id}" target="_blank">${item.primary_name}</a></strong><br>
								${typeLabel}<br>
								<hr>
							`;
							$preview.append(resultHtml);
						});
						$preview.append("<a href='find.php?q=" + query + "'>See all results</a>");
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
	})
});
