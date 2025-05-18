//script.js

document.addEventListener("DOMContentLoaded", function () {
    // Mostrar fecha y hora actuales al cargar la página
    const fechaActual = obtenerFechaHoraSistema();
    document.getElementById("fechaRegistro").value = fechaActual;

    // Validación del formulario
    document.getElementById("formularioVisitante").addEventListener("submit", function (e) {
        const nombre = document.getElementById("nombre").value.trim().toUpperCase();
        const apellidoPaterno = document.getElementById("apellidoPaterno").value.trim().toUpperCase();
        const apellidoMaterno = document.getElementById("apellidoMaterno").value.trim().toUpperCase();
        const fechaNac = document.getElementById("fechaNac").value;
        const lugarNac = document.getElementById("lugarNac").value;
        const direccion = document.getElementById("direccion").value.trim();
        const sexo = document.getElementById("sexo").value;
        const correo = document.getElementById("correo").value.trim();

        const mensajes = [];

        if (!nombre || !/^[a-zA-ZÀ-ÿ\s]+$/u.test(nombre)) {
            mensajes.push("El nombre debe contener solo letras.");
        }

        if (!apellidoPaterno || !/^[a-zA-ZÀ-ÿ]+$/u.test(apellidoPaterno)) {
            mensajes.push("El apellido paterno debe contener solo letras.");
        }

        if (!apellidoMaterno || !/^[a-zA-ZÀ-ÿ]+$/u.test(apellidoMaterno)) {
            mensajes.push("El apellido materno debe contener solo letras.");
        }

        if (!fechaNac) {
            mensajes.push("La fecha de nacimiento es obligatoria.");
        }

        if (!lugarNac) {
            mensajes.push("Debes seleccionar un lugar de nacimiento.");
        }

        if (!direccion || direccion.length < 5) {
            mensajes.push("La dirección actual debe tener al menos 5 caracteres.");
        }

        if (!sexo) {
            mensajes.push("Debes seleccionar tu sexo.");
        }

        if (!correo || !validarCorreo(correo)) {
            mensajes.push("El correo electrónico no es válido.");
        }

        if (mensajes.length > 0) {
            e.preventDefault(); // Evita enviar si hay errores
            mostrarMensaje(mensajes.join("<br>"), "error");
        } else {
            mostrarMensaje("Datos completos. Enviando formulario...", "exito");
        }
    });

    // Evento para generar CURP y RFC cuando se llenan los datos necesarios
    document.querySelectorAll("#nombre, #apellidoPaterno, #apellidoMaterno, #fechaNac, #lugarNac, #sexo").forEach(input => {
        input.addEventListener("input", generarDatosAutomaticos);
    });
});

// === FUNCIONES AUXILIARES ===

/**
 * Obtiene la fecha y hora actual del sistema en formato DD/MM/YYYY HH:mm:ss
 * @returns {string} Fecha y hora formateadas
 */
function obtenerFechaHoraSistema() {
    const fecha = new Date();

    // Obtener día, mes y año
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0'); // Enero es 0
    const anio = fecha.getFullYear();

    // Obtener horas, minutos y segundos
    const horas = String(fecha.getHours()).padStart(2, '0');
    const minutos = String(fecha.getMinutes()).padStart(2, '0');
    const segundos = String(fecha.getSeconds()).padStart(2, '0');

    // Formato final: DD/MM/YYYY HH:mm:ss
    return `${dia}/${mes}/${anio} ${horas}:${minutos}:${segundos}`;
}

/**
 * Valida correo electrónico con una expresión regular
 */
function validarCorreo(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Muestra mensaje de éxito o error
 */
function mostrarMensaje(mensaje, tipo) {
    const div = document.getElementById("mensajes");
    div.innerHTML = mensaje;
    div.className = tipo === "exito" ? "mensaje-exito" : "mensaje-error";
}

/**
 * Genera CURP y RFC basados en los datos del formulario
 */
function generarDatosAutomaticos() {
    const nombre = document.getElementById("nombre").value.trim().toUpperCase();
    const apellidoPaterno = document.getElementById("apellidoPaterno").value.trim().toUpperCase();
    const apellidoMaterno = document.getElementById("apellidoMaterno").value.trim().toUpperCase();
    const fechaNac = document.getElementById("fechaNac").value;
    const lugarNac = document.getElementById("lugarNac").value;
    const sexo = document.getElementById("sexo").value;

    if (!nombre || !apellidoPaterno || !apellidoMaterno || !fechaNac || !lugarNac || !sexo) return;

    // Limpiar apellidos y nombres de acentos
    const limpiarTexto = (texto) => {
        return texto.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    };

    const apellidoPaternoLimpio = limpiarTexto(apellidoPaterno);
    const apellidoMaternoLimpio = limpiarTexto(apellidoMaterno);
    const nombreLimpio = limpiarTexto(nombre);

    // 1. Primera letra del primer apellido
    const primLetraPaterno = apellidoPaternoLimpio.charAt(0).toUpperCase();

    // 2. Primera vocal interna del primer apellido
    const vocalInterna = Array.from(apellidoPaternoLimpio.slice(1)).find(letra =>
        "AEIOU".includes(letra.toUpperCase())
    ) || "X";

    // 3. Primera letra del segundo apellido
    const primLetraMaterno = apellidoMaternoLimpio.charAt(0).toUpperCase();

    // 4. Primera letra del nombre de pila
    const primLetraNombre = nombreLimpio.charAt(0).toUpperCase();

    // === OBTENER CONSONANTES INTERNAS ===
    function obtenerConsonanteInterna(texto) {
        for (let i = 1; i < texto.length; i++) {
            const letra = texto[i];
            if (letra && /^[B-DF-HJ-NP-TV-Z]$/i.test(letra)) {
                return letra.toUpperCase();
            }
        }
        return "X"; // Si no encuentra consonante interna
    }

    const consonantePaterno = obtenerConsonanteInterna(apellidoPaternoLimpio);
    const consonanteMaterno = obtenerConsonanteInterna(apellidoMaternoLimpio);
    const consonanteNombre = obtenerConsonanteInterna(nombreLimpio);

    // Procesar fecha correctamente
    const [anio, mes, dia] = fechaNac.split('-');
    const yy = anio.slice(-2);
    const mm = mes;
    const dd = dia;

    // Entidad federativa
    const entidad = lugarNac;

    // Género
    const genero = sexo[0].toUpperCase();

    // === GENERAR CURP COMPLETA ===
    const curpCompleta = primLetraPaterno +
                         vocalInterna +
                         primLetraMaterno +
                         primLetraNombre +
                         yy + mm + dd +
                         genero +
                         entidad +
                         consonantePaterno +
                         consonanteMaterno +
                         consonanteNombre +
                         generarCaracterAleatorio() +
                         generarDigitoVerificador();

    // === GENERAR RFC BASE ===
    const rfcBase = curpCompleta.slice(0, 10) + generarHomoclaveRFC();

    // Asignar valores generados
    document.getElementById("curp").value = curpCompleta;
    document.getElementById("rfc").value = rfcBase;
}

// === FUNCIONES DE APOYO ===

/**
 * Genera un carácter aleatorio entre A y Z
 */
function generarCaracterAleatorio() {
    const letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    return letras[Math.floor(Math.random() * letras.length)];
}

/**
 * Genera un dígito verificador entre 1 y 9
 */
function generarDigitoVerificador() {
    return Math.floor(Math.random() * 9) + 1; // Entre 1 y 9
}

/**
 * Genera la homoclave del RFC: 1 letra + 2 números
 */
function generarHomoclaveRFC() {
    const letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const letra = letras[Math.floor(Math.random() * letras.length)];
    const num1 = Math.floor(Math.random() * 10);
    const num2 = Math.floor(Math.random() * 10);
    return `${letra}${num1}${num2}`;
}