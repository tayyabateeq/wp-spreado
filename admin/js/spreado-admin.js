(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
document.addEventListener('DOMContentLoaded', function() {
	var changeButton = document.getElementById('change-keys');
	var publicKeyField = document.getElementById('spreado_public_key');
	var privateKeyField = document.getElementById('spreado_private_key');
	var saveButton = document.querySelector('input[name="submit"]');

	if (changeButton) {
		changeButton.addEventListener('click', function() {
			publicKeyField.removeAttribute('readonly');
			privateKeyField.removeAttribute('readonly');
			changeButton.style.display = 'none';
			saveButton.style.display = 'inline-block';
		});
	}

	document.getElementById('spreado-form').addEventListener('submit', function(event) {
		var publicKey = publicKeyField.value.trim();
		var privateKey = privateKeyField.value.trim();

		if (!publicKey || !privateKey) {
			event.preventDefault();
			alert('Both Public Key and Private Key are required.');
		}
	});

	document.getElementById('spreado-toggle-private-key').addEventListener('click', function() {
		var privateKeyInput = privateKeyField;
		var eyeIcon = document.getElementById('private-key-eye-icon');
		if (privateKeyInput.type === 'password') {
			privateKeyInput.type = 'text';
			eyeIcon.classList.remove('dashicons-visibility');
			eyeIcon.classList.add('dashicons-hidden');
		} else {
			privateKeyInput.type = 'password';
			eyeIcon.classList.remove('dashicons-hidden');
			eyeIcon.classList.add('dashicons-visibility');
		}
	});
});