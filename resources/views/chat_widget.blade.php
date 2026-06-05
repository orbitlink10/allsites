<style>
    /* Chat widget container (hidden by default) */
    #chat-widget {
      position: fixed;
      bottom: 90px; /* Sits above the toggle button */
      right: 20px;
      width: 300px;
      max-width: 90%;
      z-index: 99999;
      font-family: Arial, sans-serif;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      border-radius: 5px;
      overflow: hidden;
      display: none;
      background: #fff;
    }

    /* Chat widget header */
    #chat-header {
      background-color: #25D366;
      color: #fff;
      padding: 10px;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    #chat-header .chat-title {
      font-size: 16px;
      font-weight: bold;
    }
    
    #chat-header .chat-close {
      font-size: 18px;
      cursor: pointer;
    }
    
    /* Chat widget body */
    #chat-body {
      padding: 15px;
      color: #333;
      font-size: 14px;
      line-height: 1.4;
    }
    
    #chat-body p {
      margin: 0 0 15px;
    }
    
    /* Start chat button */
    #start-chat {
      display: block;
      text-align: center;
      padding: 10px;
      background-color: #25D366;
      color: #fff;
      text-decoration: none;
      border-radius: 3px;
      font-size: 16px;
    }
    
    /* Floating toggle button container */
    #chat-toggle-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 99999;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Tooltip shown only when the chat launcher is interacted with */
    .chat-tooltip {
      background-color: #333;
      color: #fff;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      position: absolute;
      bottom: 80px; /* Adjust position above the button */
      right: 0;
      opacity: 0;
      visibility: hidden;
      transform: translateY(6px);
      pointer-events: none;
      transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
    }

    #chat-toggle-container:hover .chat-tooltip,
    #chat-toggle-container:focus-within .chat-tooltip {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    /* Arrow below the sticky text */
    .chat-tooltip::after {
      content: "";
      position: absolute;
      top: 100%;
      right: 10px;
      border-width: 5px;
      border-style: solid;
      border-color: #333 transparent transparent transparent;
    }
    
    /* Floating toggle button */
    #chat-toggle-button {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      border: 0;
      background-color: #25D366;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }

    #chat-toggle-button:focus-visible {
      outline: 3px solid rgba(37, 211, 102, .35);
      outline-offset: 3px;
    }
    
    #chat-toggle-button img {
      width: 30px;
      height: 30px;
    }
  </style>

  <!-- Chat widget -->
  <div id="chat-widget">
    <div id="chat-header">
      <span class="chat-title">Chat with us on WhatsApp</span>
      <span class="chat-close" id="chat-close">&times;</span>
    </div>
    <div id="chat-body">
      <p>Hello! How can we help you today?</p>
      <a id="start-chat" href="https://wa.me/{{ get_option('whatsapp_phone') }}" target="_blank">
        Start Chat
      </a>
    </div>
  </div>

  <!-- Floating toggle button container -->
  <div id="chat-toggle-container">
    <!-- Sticky text above the toggle button -->
    <div class="chat-tooltip">Chat with us on WhatsApp</div>
    <!-- Toggle button -->
    <button type="button" id="chat-toggle-button" aria-label="Open WhatsApp chat">
      <img src="https://cdn-icons-png.flaticon.com/512/124/124034.png" alt="WhatsApp Chat">
    </button>
  </div>

  <!-- JavaScript for widget toggle functionality -->
  <script>
    (function () {
      var chatWidget = document.getElementById('chat-widget');
      var chatToggleContainer = document.getElementById('chat-toggle-container');
      var chatToggleButton = document.getElementById('chat-toggle-button');
      var chatCloseButton = document.getElementById('chat-close');

      if (!chatWidget || !chatToggleContainer || !chatToggleButton || !chatCloseButton) {
        return;
      }

      chatToggleButton.addEventListener('click', function() {
        chatWidget.style.display = 'block';
        chatToggleContainer.style.display = 'none';
      });

      chatCloseButton.addEventListener('click', function() {
        chatWidget.style.display = 'none';
        chatToggleContainer.style.display = 'flex';
      });
    })();
  </script>
