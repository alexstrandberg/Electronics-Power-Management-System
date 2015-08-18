function formhash(form, password) {
   // Create a new element input, this will be out hashed password field.
   var p = document.createElement("input");
   
   p.name = "p";
   p.type = "hidden";
   p.value = hex_sha512(password.value);
   // Add the new element to our form.
   form.appendChild(p);
   // Make sure the plaintext password doesn't get sent.
   password.value = "";
   
   // Hide login area, replace with loading screen
   document.getElementById('login_box').style.display = 'none';
   document.getElementById('loading_screen').style.display = 'inline';
   
   // Finally submit the form.
   form.submit();
}