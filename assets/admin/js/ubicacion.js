const funcionInit = () => {
	if (!"geolocation" in navigator) {
		return alert("Tu navegador no soporta el acceso a la ubicación. Intenta con otro");
	}

	const RUTA_API = "./loguear.php";

	let idWatcher = null;

	const $latitud = document.querySelector("#latitud"),
		$longitud = document.querySelector("#longitud"),
		$btnIniciar = document.querySelector("#btnIniciar"),
		$btnDetener = document.querySelector("#btnDetener"),
		$log = document.querySelector("#log");


	const onUbicacionConcedida = ubicacion => {
		const coordenadas = ubicacion.coords;
		$latitud.innerText = coordenadas.latitude;
		$longitud.innerText = coordenadas.longitude;
		//loguear(`${ubicacion.timestamp}: ${coordenadas.latitude},${coordenadas.longitude}`);
		//enviarAServidor(ubicacion);
	}

	const enviarAServidor = ubicacion => {
		// Debemos crear otro objeto porque el que nos mandan no se puede codificar
		const otraUbicacion = {
			coordenadas: {
				latitud: ubicacion.coords.latitude,
				longitud: ubicacion.coords.longitude,
			},
			timestamp: ubicacion.timestamp,
		};
		console.log("Enviando: ", otraUbicacion);
		fetch(RUTA_API, {
			method: "POST",
			body: JSON.stringify(otraUbicacion),
		}); // No esperamos el then porque no hacemos nada cuando se termine
	};

	const loguear = texto => {
		$log.innerText += "\n" + texto;
	};

	const onErrorDeUbicacion = err => {

		$latitud.innerText = "Error obteniendo ubicación: " + err.message;
		$longitud.innerText = "Error obteniendo ubicación: " + err.message;
		console.log("Error obteniendo ubicación: ", err);
	}

	const detenerWatcher = () => {
		if (idWatcher) {
			navigator.geolocation.clearWatch(idWatcher);
		}
	}

	const opcionesDeSolicitud = {
		enableHighAccuracy: true, // Alta precisión
		maximumAge: 0, // No queremos caché
		timeout: 5000 // Esperar solo 5 segundos
	};


	$btnIniciar.addEventListener("click", () => {
		alert('Under pressure');
		detenerWatcher();
		idWatcher = navigator.geolocation.watchPosition(onUbicacionConcedida, onErrorDeUbicacion, opcionesDeSolicitud);
	});

	$btnDetener.addEventListener("click", detenerWatcher);

	$latitud.innerText = "Cargando...";
	$longitud.innerText = "Cargando...";


};
document.addEventListener("DOMContentLoaded", funcionInit);
