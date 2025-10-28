// assets/js/checkTeam.js
async function checkTeamName(teamName, statusEl) {
  if (!teamName || teamName.trim().length < 2) {
    statusEl.textContent = '';
    return;
  }
  try {
    const resp = await fetch('../backend/check_team.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'team_name=' + encodeURIComponent(teamName.trim())
    });
    const text = (await resp.text()).trim();
    if (text === 'taken') {
      statusEl.textContent = 'Team name already registered ❌';
      statusEl.style.color = '#fca5a5';
    } else {
      statusEl.textContent = 'Available ✅';
      statusEl.style.color = '#bbf7d0';
    }
  } catch (err) {
    statusEl.textContent = '';
  }
}
