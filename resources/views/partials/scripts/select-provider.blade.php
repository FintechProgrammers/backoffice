 <script>
     function selectPaymentProvider(element) {
         var radioButton = element.querySelector('input[type="radio"]');
         radioButton.checked = true;
         highlightContainer(radioButton);
     }

     function highlightContainer(radioButton) {
         // Remove highlight from all containers
         document.querySelectorAll('.shipping-method-container').forEach(function(container) {
             container.classList.remove('highlighted');
         });
         // Add highlight to the selected container
         if (radioButton.checked) {
             radioButton.closest('.shipping-method-container').classList.add('highlighted');
         }
     }
 </script>
