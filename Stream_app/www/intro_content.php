<div class="w3-center">
  <h1>Καλώς ήρθατε στο <b>StreamApp</b></h1>
  <p>Επιλέξτε μια ενότητα για να μάθετε περισσότερα:</p>

  <div class="tab">
      <button class="tablinks" onclick="openTab(event, 'Σκοπός')">Σκοπός</button>
      <button class="tablinks" onclick="openTab(event, 'Εγγραφή')">Εγγραφή</button>
      <button class="tablinks" onclick="openTab(event, 'Γιατί')">Γιατί</button>
  </div>

  <div id="Σκοπός" class="tabcontent w3-container w3-animate-opacity" style="display:none">
      <h3>Σκοπός</h3>
      <p>Σκοπός του συγκεκριμένου ιστοτόπου είναι να επιτρέψει στους χρήστες να δημιουργήσουν δικές τους λίστες περιεχομένου ροής. Επιπλέον, έχουν την δυνατότητα να 
    φτιάξουν δικά τους προφίλ, δημόσιες λίστες περιεχομένου ροής, αλλά και να ακολουθήσουν άλλους χρήστες.</p>
  </div>

  <div id="Εγγραφή" class="tabcontent w3-container w3-animate-opacity" style="display:none">
      <h3>Εγγραφή</h3>
      <p>Για την πραγματοποίηση εγγραφής, πρέπει να δημιουργήσετε έναν λογαριασμό. 
         Το όνομα χρήστη και το email πρέπει να είναι μοναδικά.<br><br>
         👉 <a href="register.php" class="w3-button w3-green">Μετάβαση στη σελίδα εγγραφής</a>
      </p>
  </div>

  <div id="Γιατί" class="tabcontent w3-container w3-animate-opacity" style="display:none">
      <h3>Γιατί;</h3>
      <p>Ο ιστότοπος προσφέρει βασικές λειτουργίες και δυνατότητα ανακάλυψης νέου περιεχομένου μέσω άλλων χρηστών.</p>
  </div>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
