$(document).ready(function() {
    
    // Inicialización suave de labels con valores preexistentes
    $('.contact-form').find('.form-control').each(function() {
      if ($(this).val()) {$(this).parent().find('label').css({ 'top': '10px', 'fontSize': '14px' });
      }
    });

    // Eventos Focus & Blur optimizados
    $('.contact-form').find('.form-control').on('focus', function() {
      $(this).parent('.input-block').addClass('focus');$(this).parent().find('label').animate({ 'top': '10px', 'fontSize': '14px' }, 250);
    }).on('blur', function() {
      if ($(this).val().length === 0) {
        $(this).parent('.input-block').removeClass('focus');$(this).parent().find('label').animate({ 'top': '25px', 'fontSize': '18px' }, 250);
      }
    });

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
    // ==========================================================================
    $('.contact-form').on('submit', function(e) {       
      $('.form-notification').remove();
    
      var incompleto = false;
      var camposVacios = [];
    
      $(this).find('.form-control').each(function() {
        if ($(this).val().trim() === '') {
          incompleto = true;
          camposVacios.push($(this).parent().find('label').text() || 'Campo requerido');
          $(this).parent('.input-block').addClass('has-error');
        } else {
          $(this).parent('.input-block').removeClass('has-error');
        }
      });
    
      if (incompleto) {
        // Si hay error, detenemos el envío al servidor PHP
        e.preventDefault(); 
        var $mensajeHtml = $('<div class="form-notification error-message"></div>');
        $mensajeHtml.html('<strong>¡Faltan campos obligatorios!</strong> Por favor, completa: ' + camposVacios.join(', ') + '.');
        $(this).find('.square-button').before($mensajeHtml).prev().hide().fadeIn(300);
      } else {
        // NUEVO: Guardar en localStorage si el formulario es válido antes del envío POST
        var datosProyecto = {
            heroElegido: $('#heroElegido').val(),
            nombre: $('#nombre').val(),
            email: $('#email').val(),
            motivo: $('#motivo').val()
        };
        localStorage.setItem('datosProyecto', JSON.stringify(datosProyecto));
      }
    });

    // ==========================================================================
    // LOGICA RENDER DATA RESUMEN.HTML / RESUMEN.PHP
    // ==========================================================================
    // Verificamos si existe el contenedor que crearemos en resumen.php
    if ($('#resumen-hero').length > 0) {
        var datosGuardados = localStorage.getItem('datosProyecto');
        
        if (datosGuardados) {
            try {
                var datos = JSON.parse(datosGuardados);
                // Rellenamos el HTML con los datos cacheados
                $('#resumen-hero').text(datos.heroElegido || "No especificado");
                $('#resumen-nombre').text(datos.nombre || "No especificado");
                $('#resumen-email').text(datos.email || "No especificado");
                $('#resumen-motivo').text(datos.motivo || "No especificado");
                
                // Forzamos a que se muestre el contenedor de datos y ocultamos el mensaje vacío
                $('#contenedor-datos').show();
                $('#mensaje-vacio').hide();
            } catch (error) {
                console.error("Error localStorage:", error);
                mostrarMensajeVacio();
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
});