<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

//τίτλος σελίδας
$page_title = "Κεντρικό Μενού";

ob_start();
?>
<!-- CSS -->
<style>
.dashboard-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    overflow: hidden;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
.stats-card {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
}
.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0;
}
.stats-label {
    font-size: 1rem;
    opacity: 0.9;
}
.feature-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}
.welcome-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 10px;
    color: white;
    margin-bottom: 30px;
    text-align: center;
}
.quick-action {
    padding: 15px;
    border-left: 4px solid #667eea;
    background-color: #f8f9fa;
    margin-bottom: 15px;
    border-radius: 4px;
}
#content-container {
    min-height: 500px;
}
.loading {
    text-align: center;
    padding: 50px;
    font-size: 1.5rem;
}
</style>

<div class="w3-container">
    <!-- Welcome Header -->
    <div class="welcome-header">
        <h1><i class="fa fa-play-circle"></i> Καλώς ήρθες, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Χρήστη'); ?>!</h1>
        <p>Διαχειριστείτε τα πάντα απο εδώ</p>
    </div>

    <!-- Statistics Cards - θα ανανεώνονται μέσω AJAX -->
    <div id="stats-container" class="w3-row-padding w3-margin-bottom">
        <div class="loading">Φόρτωση στατιστικών...</div>
    </div>

    <!-- Quick Actions -->
    <div class="w3-panel w3-light-grey w3-leftbar w3-border-blue">
        <h3><i class="fa fa-bolt"></i> Γρήγορες Ενέργειες</h3>
        <div class="quick-action">
            <a href="javascript:void(0)" onclick="loadPage('youtube_search.php')" class="w3-button w3-blue w3-round"><i class="fa fa-search"></i> Αναζήτηση YouTube</a>
            <a href="javascript:void(0)" onclick="loadPage('my_lists.php')" class="w3-button w3-green w3-round" style="margin-left:10px;"><i class="fa fa-plus"></i> Νέα Λίστα</a>
        </div>
    </div>

    <div class="w3-row-padding w3-margin-top">

        <!-- Περιεχόμενο -->
        <div class="w3-third">
            <div class="w3-card w3-padding dashboard-card">
                <h3 class="w3-center"><i class="fa fa-television"></i> Περιεχόμενο</h3>
                <ul class="w3-ul w3-hoverable">
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('youtube_search.php')" class="w3-text-blue"><i class="fa fa-search"></i> Αναζήτηση YouTube</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('view_contents.php')" class="w3-text-blue"><i class="fa fa-folder-open"></i> Δες όλα τα περιεχόμενα</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('my_lists.php')" class="w3-text-blue"><i class="fa fa-list"></i>  Οι λίστες μου</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('search_lists.php')" class="w3-text-blue"><i class="fa fa-search-plus"></i> Αναζήτηση σε λίστες</a></li>
                </ul>
            </div>
        </div>

        <!-- Χρήστες -->
        <div class="w3-third">
            <div class="w3-card w3-padding dashboard-card">
                <h3 class="w3-center"><i class="fa fa-users"></i> Κοινότητα</h3>
                <ul class="w3-ul w3-hoverable">
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('search_users.php')" class="w3-text-blue"><i class="fa fa-user-plus"></i>  Βρες χρήστες</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('following.php')" class="w3-text-blue"><i class="fa fa-eye"></i> Ποιους ακολουθώ</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('followers.php')" class="w3-text-blue"><i class="fa fa-group"></i> Οι ακόλουθοί μου</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('profile.php')" class="w3-text-blue"><i class="fa fa-cog"></i>  Προφίλ</a></li>
                </ul>
            </div>
        </div>

        <!-- Open Data -->
        <div class="w3-third">
            <div class="w3-card w3-padding dashboard-card">
                <h3 class="w3-center"><i class="fa fa-database"></i> Δεδομένα</h3>
                <ul class="w3-ul w3-hoverable">
                    <li class="w3-padding-16"><a href="export_yaml.php"><i class="w3-text-blue"><i class="fa fa-download"></i>  Εξαγωγή λιστών (YAML)</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('profile.php#preferences')" class="w3-text-blue"><i class="fa fa-sliders"></i> Προτιμήσεις</a></li>
                    <li class="w3-padding-16"><a href="javascript:void(0)" onclick="loadPage('help.php')" class="w3-text-blue"><i class="fa fa-question-circle"></i> Βοήθεια & Οδηγοί</a></li>
                    <li class="w3-padding-16"><a href="logout.php" class="w3-text-red"><i class="fa fa-sign-out"></i> Έξοδος</a></li>
                </ul>
            </div>
        </div>

    </div>

    <!-- Κύριο περιεχόμενο -->
    <div id="content-container" class="w3-margin-top">
        <!-- Εδώ θα φορτώνεται το περιεχόμενο μέσω AJAX -->
        <div class="w3-panel w3-center">
        
            <p>Επιλέξτε μια ενέργεια από το μενού για να ξεκινήσετε</p>
        </div>
    </div>
   
</div>

<script>
// Συνάρτηση για φόρτωση σελίδας μέσω AJAX
function loadPage(pageUrl) {
    // Εμφάνιση μηνύματος φόρτωσης
    document.getElementById('content-container').innerHTML = '<div class="loading"><i class="fa fa-spinner fa-spin"></i> Φόρτωση...</div>';
    
    // Αίτημα AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', pageUrl, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                document.getElementById('content-container').innerHTML = xhr.responseText;
                
                // Ενημέρωση τίτλου σελίδας
                document.title = "StreamApp - " + extractTitleFromContent(xhr.responseText);
                
                // Προσθήκη ιστορικού περιήγησης
                history.pushState({url: pageUrl}, '', pageUrl);
            } else {
                document.getElementById('content-container').innerHTML = '<div class="w3-panel w3-red">Σφάλμα φόρτωσης σελίδας</div>';
            }
        }
    };
    xhr.send();
}

// Συνάρτηση εξαγωγής τίτλου από το περιεχόμενο
function extractTitleFromContent(content) {
    var tempDiv = document.createElement('div');
    tempDiv.innerHTML = content;
    var titleElement = tempDiv.querySelector('h1, h2, h3');
    return titleElement ? titleElement.textContent : 'Νέα Σελίδα';
}


function loadStats() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_stats.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('stats-container').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Φόρτωση αρχικών στατιστικών
loadStats();

// Χειρισμός κουμπιού πίσω του browser
window.onpopstate = function(event) {
    if (event.state && event.state.url) {
        loadPage(event.state.url);
    }
};

// Αρχική κατάσταση για το ιστορικό
history.replaceState({url: 'dashboard.php'}, '', 'dashboard.php');
</script>
<?php
$content = ob_get_clean();

//  layout
include("layout.php");

