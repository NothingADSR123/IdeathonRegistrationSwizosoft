// assets/js/main.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('regForm');
  const teamInput = document.getElementById('team_name');
  const teamStatus = document.getElementById('team-status');

  // attach team-name blur check
  teamInput.addEventListener('blur', () => {
    const name = teamInput.value;
    window.checkTeamName(name, teamStatus);
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Registering...';

    const fd = new FormData(form);

    try {
      const res = await fetch('../backend/register.php', { method: 'POST', body: fd });
      const json = await res.json();

      if (json.status === 'success') {
        // redirect to success page with team and id
        const q = new URLSearchParams({ team: json.team, id: json.id });
        window.location.href = `../pages/success.html?${q.toString()}`;
      } else {
        alert(json.message || 'Registration failed. Check console.');
        console.error('register error:', json);
      }
    } catch (err) {
      alert('Network or server error.');
      console.error(err);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Register';
    }
  });
});
