/**
 * Script para o painel administrativo do WhatsApp Flutuante WP
 */

(function($) {
  'use strict';
  
  // Função principal
  $(document).ready(function() {
      // Inicializar o color picker
      if ($.fn.wpColorPicker) {
          $('.wpfww-color-picker').wpColorPicker();
      }
      
      // Toggle para mostrar/esconder as configurações do botão
      $('#wpfww_button_enabled').on('change', function() {
          if ($(this).is(':checked')) {
              $('.wpfww-button-settings').slideDown();
          } else {
              $('.wpfww-button-settings').slideUp();
          }
      });
      
      // Trigger inicial para mostrar/esconder as configurações do botão
      if (!$('#wpfww_button_enabled').is(':checked')) {
          $('.wpfww-button-settings').hide();
      }
      
      // Toggle para mostrar/esconder as configurações de cada contato
      $('.wpfww-contact-toggle').on('change', function() {
          const contactId = $(this).data('contact-id');
          
          if ($(this).is(':checked')) {
              $(`.wpfww-contact-settings-${contactId}`).slideDown();
          } else {
              $(`.wpfww-contact-settings-${contactId}`).slideUp();
          }
      });
      
      // Trigger inicial para mostrar/esconder as configurações de cada contato
      $('.wpfww-contact-toggle').each(function() {
          const contactId = $(this).data('contact-id');
          
          if (!$(this).is(':checked')) {
              $(`.wpfww-contact-settings-${contactId}`).hide();
          }
      });
      
      // Atualizar o preview conforme as configurações são alteradas
      function updatePreview() {
          const buttonColor = $('#wpfww_button_color').val();
          const buttonPosition = $('input[name="wpfww_options[button_position]"]:checked').val();
          const buttonSize = $('#wpfww_button_size').val();
          
          // Atualizar cor
          $('.wpfww-preview-button').css('background-color', buttonColor);
          
          // Atualizar posição
          $('.wpfww-preview').css('text-align', buttonPosition === 'left' ? 'left' : 'right');
          
          // Atualizar tamanho
          let buttonWidth = 60;
          let buttonHeight = 60;
          let iconSize = 30;
          
          if (buttonSize === 'small') {
              buttonWidth = 50;
              buttonHeight = 50;
              iconSize = 25;
          } else if (buttonSize === 'large') {
              buttonWidth = 70;
              buttonHeight = 70;
              iconSize = 35;
          }
          
          $('.wpfww-preview-button').css({
              'width': buttonWidth + 'px',
              'height': buttonHeight + 'px',
              'line-height': buttonHeight + 'px'
          });
          
          $('.wpfww-preview-button svg').css({
              'width': iconSize + 'px',
              'height': iconSize + 'px'
          });
      }
      
      // Atualizar preview ao carregar a página
      updatePreview();
      
      // Atualizar preview quando as configurações mudam
      $('#wpfww_button_color').wpColorPicker({
          change: function(event, ui) {
              setTimeout(updatePreview, 100);
          }
      });
      
      $('input[name="wpfww_options[button_position]"]').on('change', updatePreview);
      $('#wpfww_button_size').on('change', updatePreview);
      
      // Validação dos números de telefone
      $('.wpfww-phone-number').on('blur', function() {
          const phoneNumber = $(this).val().replace(/[^0-9]/g, '');
          
          if (phoneNumber && (phoneNumber.length < 10 || phoneNumber.length > 15)) {
              $(this).addClass('error');
              alert('O número de telefone deve ter entre 10 e 15 dígitos.');
          } else {
              $(this).removeClass('error');
          }
      });
      
      // Confirmar antes de desativar o botão
      $('#wpfww-form').on('submit', function(e) {
          if ($('#wpfww_button_enabled').is(':checked')) {
              // Verificar se há pelo menos um contato ativo e com número
              let hasActiveContact = false;
              
              $('.wpfww-contact-toggle:checked').each(function() {
                  const contactId = $(this).data('contact-id');
                  const phoneNumber = $(`.wpfww-phone-number[data-contact-id="${contactId}"]`).val();
                  
                  if (phoneNumber) {
                      hasActiveContact = true;
                      return false; // Sair do loop each
                  }
              });
              
              if (!hasActiveContact) {
                  e.preventDefault();
                  alert('Você precisa ter pelo menos um contato habilitado e com número de telefone válido.');
                  return false;
              }
          }
      });
  });
  
})(jQuery);