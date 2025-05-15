console.log("search.js loaded successfully");

$(document).ready(function () {
	const $input = $('#search-input');
	const $preview = $('#search-output');

	let debounceTimeout; // Initialise variable for clearTimeout

	$input.on('input', function () {
		const query = $input.val().trim();
		clearTimeout(debounceTimeout);

		if (query.length < 3) {
			$preview.empty().hide();
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

					$preview.append("Showing top 5 results...<br>")

					if (data && data.length > 0) {
						data.forEach(item => {
							const result = $(`
								<strong>${item.primary_name}</strong><br> (${item.id})<br>
								`);
							$preview.append(result);
						});
						$preview.show();
					};
				},
				error: function () {
					$preview.html('<div class="list-group-item text-danger">Error fetching results</div>').show();
				}
			});
		}, 300);
	});

	$(document).on('click', function (e) {
		if (!$(e.target).closest('#search-input, #search-output').length) {
			$preview.hide();
		}
	});
});
