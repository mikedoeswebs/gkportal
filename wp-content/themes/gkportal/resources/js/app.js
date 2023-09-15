// Navigation toggle
window.addEventListener('load', function () {
	let main_navigation = document.querySelector('#primary-menu');
	document.querySelector('#primary-menu-toggle').addEventListener('click', function (e) {
		e.preventDefault();
		main_navigation.classList.toggle('hidden');
	});

	let player_filter = document.querySelector('#player-filters');
	document.querySelector('#player-filter-toggle').addEventListener('click', function (e) {
		e.preventDefault();
		player_filter.classList.toggle('hidden');
	});
});