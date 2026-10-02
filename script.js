$(document).ready(function() {
    
    // 1. Poblado dinámico del Dropdown (texto plano, sin números ni caracteres especiales)
    var $heroSelect = $('#heroElegido');
    if ($heroSelect.length) {
        $('.hero-card-section > h1 a').each(function() {
            // Regex: Mantiene solo letras (mayúsculas/minúsculas) y espacios
            var nombrePlano = $(this).text().replace(/[^a-zA-Z\s]/g, '').trim();
            if (nombrePlano !== "") {
                // Se inyecta la opción con estilo negro oscuro para evitar texto blanco sobre fondo blanco nativo del <option>
                $heroSelect.append($('<option>', { value: nombrePlano, text: nombrePlano, style: "color: #050811;" }));
            }
        });
    }

    // 2. Inicialización suave de labels con valores preexistentes
    $('.contact-form').find('.form-control').each(function() {
      if ($(this).val()) {
          $(this).parent().find('label').css({ 'top': '10px', 'fontSize': '14px' });
      }
    });

    // 3. Eventos Focus, Blur y Change optimizados (Se añade 'change' para adaptar el dinamismo al select)
    $('.contact-form').find('.form-control').on('focus', function() {
      $(this).parent('.input-block').addClass('focus');
      $(this).parent().find('label').stop(true, true).animate({ 'top': '10px', 'fontSize': '14px' }, 250);
    }).on('blur change', function() {
      // Se utiliza !$(this).val() para proteger contra valores nulos en el select
      if (!$(this).val() || $(this).val().length === 0) {
        $(this).parent('.input-block').removeClass('focus');
        $(this).parent().find('label').stop(true, true).animate({ 'top': '25px', 'fontSize': '18px' }, 250);
      } else {
        $(this).parent('.input-block').addClass('focus');
        $(this).parent().find('label').stop(true, true).animate({ 'top': '10px', 'fontSize': '14px' }, 250);
      }
    });

    // ==========================================================================

    // ==========================================================================

    // ==========================================================================
    // EFECTO CINEMÁTICO DE SCROLL (STICKY REVEAL HERO)
    // ==========================================================================
    const heroWrapper = document.querySelector(".cinematic-wrapper");
    const heroBg = document.getElementById("sticky-hero");
    const heroText = document.getElementById("hero-text");

    if (heroWrapper && heroBg && heroText) {
      window.addEventListener("scroll", function() {
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        const wrapperTop = heroWrapper.getBoundingClientRect().top + scrollY;
        
        let progress = (scrollY - wrapperTop) / windowHeight;
        progress = Math.max(0, Math.min(1, progress));
        
        const scale = 1 + (progress * 15);
        const blur = progress * 20;
        const textOpacity = 1 - (progress * 1.5);
        const bgOpacity = 1 - progress;
        
        heroText.style.transform = `scale(${scale})`;
        heroText.style.filter = `blur(${blur}px)`;
        heroText.style.opacity = Math.max(textOpacity, 0);
        heroBg.style.backgroundColor = `rgba(0, 0, 0, ${Math.max(bgOpacity, 0)})`;
      });
    }

 // ==========================================================================
    // VALIDACIÓN DEL FORMULARIO PREVIO AL ENVÍO PHP
    // (RAMA PHP: toda la validación la hace resumen.php; JS solo envía y pinta los errores en index)
    // ==========================================================================
$('.contact-form').on('submit', function(e) {
  e.preventDefault();
  $('.form-notification').remove();
  $('.contact-form .input-block').removeClass('has-error');

  // 1. Guardamos los datos introducidos en el formulario
  var datosProyecto = {
    heroElegido: $('#heroElegido').val() || '',
    nombre: $('#nombre').val() || '',
    email: $('#email').val() || '',
    motivo: $('#motivo').val() || ''
  };

  // Muestra en index los errores devueltos por PHP
  function mostrarErrores(mensaje, errores) {
    var $mensajeHtml = $('<div class="form-notification error-message"></div>');
    $mensajeHtml.append($('<strong></strong>').text('¡Error!'));

    if (errores && Object.keys(errores).length > 0) {
      var $lista = $('<ul style="margin:6px 0 0 18px; padding:0;"></ul>');
      $.each(errores, function(campo, texto) {
        $lista.append($('<li></li>').text(texto));
        $('#' + campo).closest('.input-block').addClass('has-error');
      });
      $mensajeHtml.append($lista);
    } else {
      $mensajeHtml.append($('<br>')).append(document.createTextNode(mensaje || 'No se pudo validar el formulario.'));
    }
    $('.square-button').before($mensajeHtml);
  }

  // 2. Enviamos la validación por AJAX
  $.ajax({
    url: 'resumen.php',
    type: 'POST',
    data: $(this).serialize(),
    dataType: 'json',
    success: function(response) {
      if (response.status === 'error') {
        mostrarErrores(response.mensaje, response.errores);
      } else if (response.status === 'success') {
        // Guardamos en localStorage los datos válidos antes de redirigir
        localStorage.setItem('datosProyecto', JSON.stringify(datosProyecto));
        window.location.href = 'resumen.php';
      }
    },
    error: function() {
      mostrarErrores('No se pudo contactar con el servidor. Inténtalo de nuevo.');
    }
  });
});

// Al corregir un campo se quita su marca de error
$('.contact-form').find('.form-control').on('input change', function() {
  $(this).closest('.input-block').removeClass('has-error');
});

 // ==========================================================================
    // LOGICA RENDER DATA RESUMEN.HTML / RESUMEN.PHP
    // ==========================================================================
    // Verificamos si existe el contenedor que crearemos en resumen.php
    if ($('#resumen-hero').length > 0) {

        // SI PHP HA DETECTADO ERRORES, DETENEMOS LA CARGA Y LIMPIAMOS LOCALSTORAGE
        if ($('.error-box').length > 0) {
            $('#contenedor-datos').hide();
            $('#mensaje-vacio').show();
            localStorage.removeItem('datosProyecto'); 
            return;
        }
// Lectura segura de localStorage
var datosGuardados = localStorage.getItem('datosProyecto');

if (datosGuardados && datosGuardados !== "undefined") {
  try {
    var datos = JSON.parse(datosGuardados);
    
    // Rellena los campos HTML si tu plantilla usa JS para mostrarlos:
    $('#resumenHero').text(datos.heroElegido);
    $('#resumenNombre').text(datos.nombre);
    $('#resumenEmail').text(datos.email);
    $('#resumenMotivo').text(datos.motivo);

  } catch (e) {
    console.error("Error al parsear localStorage:", e);
  }
} else {
            // Validamos por si acaso el PHP inyectó datos vía POST a pesar de no haber localStorage
            if ($('#resumen-hero').text().trim() === '') {
                mostrarMensajeVacio();
            }
        }
    }

    function mostrarMensajeVacio() {
        $('#contenedor-datos').hide();
        $('#mensaje-vacio').show();
    }
    // ==========================================================================
    // INTERSECTION OBSERVER
    // ==========================================================================
    const animObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        entry.target.classList.toggle('is-visible', entry.isIntersecting);
      });
    }, { threshold: 0.25 });

    document.querySelectorAll('.hero-card-section').forEach(section => {
      if (section.querySelector('.typing-container') || section.querySelector('.reveal-container')) {
        animObserver.observe(section);
      }
    });

    // ==========================================================================
    // AUTOSCROLL AL CARGAR PÁGINA SI EXISTE ANCLA EN LA URL
    // ==========================================================================
    if (window.location.hash) {
      var initialHash = window.location.hash;
      if (initialHash === '#.contact-wrap' || initialHash === '#.contact-section-container' || initialHash === '#formulario' || initialHash === '#contacto') {
        initialHash = '#contacto, .contact-section-container';
      }
      
      var $initialTarget =$(initialHash);
      if ($initialTarget.length) {
        setTimeout(function() {
          $('html, body').animate({ scrollTop:$initialTarget.offset().top - 90 }, 600);
        }, 250);
      }
    }

    // ==========================================================================
    // MANEJADOR UNIFICADO DE DESPLAZAMIENTOS SUAVES (SCROLL)
    // ==========================================================================
    $('.trigger-catalog, .trigger-contact, .btn-primary, .btn-secondary, a[href*="#"], a[href^="."]').on('click', function(e) {
      let selectorDestino = $(this).attr('href');
      if (!selectorDestino) return;

      if (selectorDestino.startsWith('.')) {
        e.preventDefault();
        let $elem =$(selectorDestino);
        if (!$elem.length) $elem =$('#contacto');
        if ($elem.length) {$('.header-nav').removeClass('mobile-active');
          $('html, body').animate({ scrollTop:$elem.offset().top - 90 }, 800);
        }
        return;
      }

      let currentPage = window.location.pathname.split('/').pop() || 'index.html';

      if (currentPage !== 'index.html' && currentPage !== '' && selectorDestino.includes('index.html')) {
        return; 
      }

      let hash = selectorDestino.includes('#') ? selectorDestino.substring(selectorDestino.indexOf('#')) : selectorDestino;

      if (hash === '#contacto' || hash === '#.contact-wrap' || hash === '#.contact-section-container' || hash === '#formulario') {
        hash = '#contacto, .contact-section-container';
      } else if (hash === '#primer-snippet') {
        hash = '#primer-snippet';
      }

      const $targetElement =$(hash);
      if ($targetElement.length) {
        e.preventDefault();
        $('.header-nav').removeClass('mobile-active');
        $('html, body').animate({ scrollTop:$targetElement.offset().top - 90 }, 800);
      }
    });

    // Control de Header Compacto en Scroll & Menú Móvil
    window.addEventListener('scroll', function() {
      $('.main-header').toggleClass('scrolled', window.scrollY > 50);
    });

    $('.mobile-nav-toggle').on('click', function() {
      $('.header-nav').toggleClass('mobile-active');$(this).toggleClass('active-toggle');
    });
    // ==========================================================================
// EFECTO MAGNÉTICO PARA EL BOTÓN DEL HEADER
// ==========================================================================
const magneticBtn = document.querySelector('.btn-header-cta');

if (magneticBtn) {
  let btnRect = null;

  magneticBtn.addEventListener('mouseenter', function() {
    // Guarda las dimensiones y centro inicial del botón al entrar
    btnRect = this.getBoundingClientRect();
  });

  magneticBtn.addEventListener('mousemove', function(e) {
    if (!btnRect) btnRect = this.getBoundingClientRect();

    // Centro estático del botón
    const centerX = btnRect.left + btnRect.width / 2;
    const centerY = btnRect.top + btnRect.height / 2;

    // Calcular ~35% de atracción magnética hacia el cursor desde el centro
    const offsetX = (e.clientX - centerX) * 0.35;
    const offsetY = (e.clientY - centerY) * 0.35;

    this.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
  });

  magneticBtn.addEventListener('mouseleave', function() {
    // Restablecer posición al salir de la zona del botón
    this.style.transform = 'translate(0px, 0px)';
    btnRect = null;
  });
}

    // ==========================================================================
    // MENÚ MÓVIL: cierre al tocar fuera, con Escape o al ensanchar la pantalla
    // ==========================================================================
    function cerrarMenuMovil() {
      $('.header-nav').removeClass('mobile-active');
      $('.mobile-nav-toggle').removeClass('active-toggle');
    }

    $(document).on('click', function(e) {
      if ($('.header-nav').hasClass('mobile-active') &&
          !$(e.target).closest('.header-nav, .mobile-nav-toggle').length) {
        cerrarMenuMovil();
      }
    });

    $(document).on('keydown', function(e) {
      if (e.key === 'Escape') cerrarMenuMovil();
    });

    window.addEventListener('resize', function() {
      if (window.innerWidth > 992) cerrarMenuMovil();
    });
// ==========================================================================
// ACCIÓN COMBINADA PARA EL BOTÓN DEL HEADER (Abrir enlace externo + Scroll a contacto)
// ==========================================================================
$('.header-actions .btn-header-cta').on('click', function(e) {
  e.preventDefault();
  e.stopPropagation(); // Evita que el script global interfiera con este botón
  
  // 1. Abrir la URL externa en una nueva pestaña de forma segura
  var externalLink = document.createElement('a');
  externalLink.href = 'https://kinetics.colorion.co/?ref=text-effects.colorion.co';
  externalLink.target = '_blank';
  document.body.appendChild(externalLink);
  externalLink.click();
  document.body.removeChild(externalLink);
  
  // 2. Hacer el desplazamiento suave hacia #contacto en la página actual
  var $targetElement = $('#contacto');
  if ($targetElement.length) {
    $('.header-nav').removeClass('mobile-active');
    $('html, body').animate({ scrollTop: $targetElement.offset().top - 90 }, 800);
  }
});
});