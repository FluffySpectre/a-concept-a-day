// Set the today date in the title
const current_timestamp = parseFloat(document.getElementById('timestamp').innerText);
document.getElementById('today').innerText = new Date(current_timestamp * 1000).toLocaleDateString(undefined, { year:"numeric", month:"numeric", day:"numeric" });

function registerRSS() {
    window.open('/rss', '_blank');
}
