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

					if (data && data.length > 0) {
						data.forEach(item => {
							const result = $(`
                				<a href="title.php?tconst=${item.id}" class="list-group-item list-group-item-action">
                    				<strong>${item.primary_title}</strong> (${item.start_year || 'NA'})
                    				<br><small>Director: ${item.directors || 'NA'} | ${item.runtime_minutes || 'NA'} min</small>
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
