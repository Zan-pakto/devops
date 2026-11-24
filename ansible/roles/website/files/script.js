// Interactive Real-Time Clock & Telemetry Updater
document.addEventListener('DOMContentLoaded', () => {
  const clockElement = document.getElementById('clock-display');
  const targetHealth = document.getElementById('target-health');

  function updateClock() {
    const now = new Date();
    if (clockElement) {
      clockElement.textContent = now.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    }
  }

  updateClock();
  setInterval(updateClock, 1000);

  // Periodic heartbeat animation
  console.log('Automated DevOps System Showcase initialized.');
  console.log('Target Node: web-target.devops.lab [Apache/2.4 on Ubuntu 22.04 LTS]');
  console.log('Configuration Engine: Puppet Master/Agent');
  console.log('Deployment Engine: Ansible Playbook (Agentless SSH)');
  console.log('Monitoring Engine: Nagios Core 4.x (HTTP & ICMP)');
});
