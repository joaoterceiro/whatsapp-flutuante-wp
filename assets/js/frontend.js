/**
 * Script para o botão flutuante de WhatsApp no frontend
 * WhatsApp Flutuante WP
 */

(function($) {
  'use strict';
  
  // Função principal
  function initWhatsAppButton() {
      const whatsappButton = document.getElementById('whatsapp-button');
      
      if (!whatsappButton) {
          return;
      }
      
      // Verificar se temos contatos disponíveis nos dados passados pelo PHP
      if (!wpfww_data || !wpfww_data.contacts || !wpfww_data.contacts.length) {
          console.warn('WhatsApp Flutuante WP: Nenhum contato ativo configurado.');
          return;
      }
      
      // Adicionar evento de clique no botão
      whatsappButton.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Obter um contato aleatório
          const randomContact = getRandomContact();
          
          // Criar URL do WhatsApp e abrir em nova janela
          const whatsappURL = getWhatsAppURL(randomContact);
          window.open(whatsappURL, '_blank');
      });
  }
  
  // Função para obter um contato aleatório
  function getRandomContact() {
      const randomIndex = Math.floor(Math.random() * wpfww_data.contacts.length);
      return wpfww_data.contacts[randomIndex];
  }
  
  // Função para criar URL do WhatsApp
  function getWhatsAppURL(contact) {
      const encodedMessage = encodeURIComponent(contact.message);
      return `https://wa.me/${contact.number}?text=${encodedMessage}`;
  }
  
  // Inicializar quando o DOM estiver pronto
  $(document).ready(function() {
      initWhatsAppButton();
  });
  
  // Inicializar também após o carregamento da página (para compatibilidade com temas que carregam conteúdo de forma assíncrona)
  $(window).on('load', function() {
      initWhatsAppButton();
  });
  
})(jQuery);