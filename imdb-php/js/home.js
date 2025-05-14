console.log("home.js loaded successfully");

$(document).ready(function () {
	const $input = $('#search-input');
	const $preview = $('#search-preview');

	let debounceTimeout;

	$input.on('input', function () {
		const query = $input.val().trim();
		clearTimeout(debounceTimeout);

		if (query.length < 2) {
			$preview.empty().hide();
			return;
		}

		debounceTimeout = setTimeout(function () {
			$.ajax({
				url: 'api.php',
				method: 'GET',
				data: {
					q: 'titles',
					title: query,
					limit: 5,
					offset: 0
				},
				dataType: 'json',
				success: function (data) {
					console.log(data); // Log data for debug
					$preview.empty();  // Clear previous results

					$preview.append("Showing top 5 results...<br>")

					if (data && data.length > 0) {
						data.forEach(item => {
							const result = $(`
                				<a href="title.php?tconst=${item.id}" class="list-group-item list-group-item-action">
                    				<strong>${item.primary_title}</strong> (${item.start_year || 'Unknown'})
                    				<br><small>${item.title_type ? `${item.title_type} | ` : ''} Director: ${item.directors || 'Unknown'}${item.runtime_minutes ? ` | ${item.runtime_minutes} min` : ''}</small>
                				</a>
            					`);
							$preview.append(result);
						});
						$preview.show();
					} else {
						$preview.html('<div class="list-group-item text-muted">No results found</div>').show();
					}
				},
				error: function () {
					$preview.html('<div class="list-group-item text-danger">Error fetching results</div>').show();
				}
			});
		}, 300);
	});

	$(document).on('click', function (e) {
		if (!$(e.target).closest('#search-input, #search-preview').length) {
			$preview.hide();
		}
	});
});
