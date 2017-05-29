var map;
ymaps.ready(init);

function init(){
	map = new ymaps.Map('map', {
		center: [55.76592284824946,37.619928108459455],
		zoom: 16
	}, {
		searchControlProvider: 'yandex#search'
	});
	
	let marker = new ymaps.GeoObject({
			geometry: {
				type: "Point",
				coordinates: [55.76592284824946,37.619928108459455]
			}
		});;
	
	map.geoObjects.add( marker );
}
